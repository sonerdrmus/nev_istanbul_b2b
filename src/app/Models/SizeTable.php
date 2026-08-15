<?php

namespace App\Models;

use App\Models\Concerns\FillsLocalizedNameFromCatalog;
use App\Models\Concerns\HasLocalizedName;
use App\Models\Concerns\SyncsLinkedProductVariationOptions;
use App\Support\CatalogLabelTranslator;
use App\Support\LocaleContent;
use Illuminate\Database\Eloquent\Model;

class SizeTable extends Model
{
    use FillsLocalizedNameFromCatalog;
    use HasLocalizedName;
    use SyncsLinkedProductVariationOptions;

    protected $fillable = [
        'name',
        'name_en',
        'name_it',
        'slug',
        'title',
        'title_en',
        'title_it',
        'trigger_variation_name',
        'trigger_option_value',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::saving(function (SizeTable $table): void {
            $source = trim((string) ($table->title ?: $table->name ?: ''));
            if ($source === '') {
                return;
            }
            $pair = CatalogLabelTranslator::fillPair($source, $table->title_en, $table->title_it);
            if (blank($table->title_en)) {
                $table->title_en = $pair['en'] !== '' ? $pair['en'] : null;
            }
            if (blank($table->title_it)) {
                $table->title_it = $pair['it'] !== '' ? $pair['it'] : null;
            }
        });
    }

    public function getLocalizedTitleAttribute(): string
    {
        $tr = trim((string) ($this->title ?: $this->name ?: ''));

        return LocaleContent::display(
            $tr,
            $this->title_en ?: $this->name_en,
            $this->title_it ?: $this->name_it,
        );
    }

    protected static function linkedProductVariationType(): string
    {
        return 'size_table';
    }

    public function productVariationOptions()
    {
        return $this->hasMany(ProductVariationOption::class, 'size_table_id');
    }

    public function columns()
    {
        return $this->hasMany(SizeTableColumn::class)->orderBy('sort_order');
    }
}
