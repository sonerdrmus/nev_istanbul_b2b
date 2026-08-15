<?php

namespace App\Models;

use App\Support\CatalogLabelTranslator;
use App\Support\LocaleContent;
use App\Support\MachineTranslator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'name_en',
        'name_it',
        'slug',
        'image_path',
        'sort_order',
        'is_active',
        'show_in_top_menu',
        'top_menu_sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_in_top_menu' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Category $category): void {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
        static::saving(function (Category $category): void {
            if (filled($category->name)) {
                $pair = CatalogLabelTranslator::fillPair($category->name, $category->name_en, $category->name_it);
                if (blank($category->name_en)) {
                    $translated = MachineTranslator::translate((string) $category->name, 'tr', 'en');
                    $category->name_en = filled($translated) ? $translated : $pair['en'];
                }
                if (blank($category->name_it)) {
                    $category->name_it = $pair['it'] !== '' ? $pair['it'] : null;
                }
            }
            if (! $category->parent_id) {
                return;
            }
            if ((int) $category->parent_id === (int) $category->id) {
                $category->parent_id = null;
                return;
            }
            // Kendisinin veya bir alt kategorisinin üstü yapılamaz (döngü engelleme)
            $descendantIds = $category->exists ? $category->getDescendantIds() : [];
            if (in_array((int) $category->parent_id, array_merge([$category->id], $descendantIds), true)) {
                $category->parent_id = null;
            }
        });
    }

    public function getLocalizedNameAttribute(): string
    {
        return LocaleContent::display($this->name, $this->name_en, $this->name_it);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /** Üst zinciri yüklemek için (dropdown etiketinde tam yol göstermek üzere). */
    public function parentRecursive()
    {
        return $this->belongsTo(Category::class, 'parent_id')->with('parentRecursive');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order')->orderBy('name');
    }

    /** Bu kategorinin tüm alt kategorilerinin id'leri (döngü engellemek için). */
    public function getDescendantIds(): array
    {
        $ids = [];
        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->getDescendantIds());
        }
        return $ids;
    }

    /** Kökten bu kategoriye kadar tam yol (örn. "Üst Giyim › Tişört › Erkek"). */
    public function getFullPathAttribute(): string
    {
        $parts = [];
        $current = $this;
        while ($current) {
            array_unshift($parts, $current->name);
            $current = $current->parent;
        }
        return implode(' › ', $parts);
    }

    /** Derinlik: 0 = kök, 1 = bir alt seviye, vb. */
    public function getDepthAttribute(): int
    {
        $depth = 0;
        $current = $this->parent;
        while ($current) {
            $depth++;
            $current = $current->parent;
        }
        return $depth;
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Mağaza listesi / üst menü: ürünler hangi category_id’lerde aranır (StoreController ile aynı mantık).
     *
     * @return array<int>
     */
    public function categoryIdsForProductListing(): array
    {
        $this->loadMissing('children');

        if ($this->children->isNotEmpty()) {
            return $this->children->pluck('id')->all();
        }

        return [$this->id];
    }

    /**
     * Üst kategoriler + alt kategoriler (sadece aktif, sıralı).
     */
    public static function treeForMenu(): \Illuminate\Support\Collection
    {
        return static::with(['children' => fn ($q) => $q->active()->orderBy('sort_order')->orderBy('name')])
            ->active()
            ->roots()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /** Admin panelden seçilen üst menü kategorileri. */
    public static function topMenuForStore(): \Illuminate\Support\Collection
    {
        return static::with(['children' => fn ($q) => $q->active()->orderBy('sort_order')->orderBy('name')])
            ->active()
            ->where('show_in_top_menu', true)
            ->orderBy('top_menu_sort_order')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}
