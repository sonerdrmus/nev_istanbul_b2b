<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('interface_packaging_preference_variations')) {
            Schema::table('interface_packaging_preference_variations', function (Blueprint $table) {
                if (! Schema::hasColumn('interface_packaging_preference_variations', 'slug')) {
                    $table->string('slug', 64)->nullable()->after('name');
                }
                if (! Schema::hasColumn('interface_packaging_preference_variations', 'requires_material')) {
                    $table->boolean('requires_material')->default(false)->after('image_path');
                }
            });

            if (Schema::hasColumn('interface_packaging_preference_variations', 'price_multiplier')) {
                Schema::table('interface_packaging_preference_variations', function (Blueprint $table) {
                    $table->dropColumn('price_multiplier');
                });
            }
        }

        if (! Schema::hasTable('interface_packaging_materials')) {
            Schema::create('interface_packaging_materials', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug', 64);
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('interface_packaging_customizations')) {
            Schema::create('interface_packaging_customizations', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug', 64);
                $table->decimal('extra_price', 12, 2)->default(0);
                $table->boolean('is_default')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('interface_packaging_settings')) {
            Schema::create('interface_packaging_settings', function (Blueprint $table) {
                $table->id();
                $table->boolean('barcode_enabled')->default(true);
                $table->string('barcode_label')->default('Barkod / Etiket Alanı İstiyorum');
                $table->decimal('barcode_extra_price', 12, 2)->default(0);
                $table->text('barcode_description')->nullable();
                $table->string('barcode_image_path')->nullable();
                $table->timestamps();
            });
        }

        $this->seedDefaults();
    }

    public function down(): void
    {
        Schema::dropIfExists('interface_packaging_settings');
        Schema::dropIfExists('interface_packaging_customizations');
        Schema::dropIfExists('interface_packaging_materials');

        if (Schema::hasTable('interface_packaging_preference_variations')) {
            Schema::table('interface_packaging_preference_variations', function (Blueprint $table) {
                if (Schema::hasColumn('interface_packaging_preference_variations', 'requires_material')) {
                    $table->dropColumn('requires_material');
                }
                if (Schema::hasColumn('interface_packaging_preference_variations', 'slug')) {
                    $table->dropColumn('slug');
                }
                if (! Schema::hasColumn('interface_packaging_preference_variations', 'price_multiplier')) {
                    $table->decimal('price_multiplier', 12, 4)->default(1)->after('name');
                }
            });
        }
    }

    private function seedDefaults(): void
    {
        if (! Schema::hasTable('interface_packaging_preference_variations')) {
            return;
        }

        $now = now();

        if (DB::table('interface_packaging_preference_variations')->count() === 0) {
            DB::table('interface_packaging_preference_variations')->insert([
                [
                    'name' => 'OPP Şeffaf',
                    'slug' => 'opp_transparent',
                    'image_path' => null,
                    'requires_material' => false,
                    'sort_order' => 0,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Kilitli Poşet',
                    'slug' => 'zip_lock',
                    'image_path' => null,
                    'requires_material' => true,
                    'sort_order' => 10,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);
        } else {
            DB::table('interface_packaging_preference_variations')
                ->whereNull('slug')
                ->orWhere('slug', '')
                ->orderBy('id')
                ->get()
                ->each(function ($row, int $index) use ($now): void {
                    $slug = match ($index) {
                        0 => 'opp_transparent',
                        1 => 'zip_lock',
                        default => 'packaging_'.$row->id,
                    };
                    $requiresMaterial = $slug === 'zip_lock';
                    DB::table('interface_packaging_preference_variations')
                        ->where('id', $row->id)
                        ->update([
                            'slug' => $slug,
                            'requires_material' => $requiresMaterial,
                            'updated_at' => $now,
                        ]);
                });
        }

        if (Schema::hasTable('interface_packaging_materials') && DB::table('interface_packaging_materials')->count() === 0) {
            DB::table('interface_packaging_materials')->insert([
                ['name' => 'Şeffaf', 'slug' => 'transparent', 'sort_order' => 0, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Mat', 'slug' => 'matte', 'sort_order' => 10, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (Schema::hasTable('interface_packaging_customizations') && DB::table('interface_packaging_customizations')->count() === 0) {
            DB::table('interface_packaging_customizations')->insert([
                ['name' => 'Standart Baskı', 'slug' => 'standard', 'extra_price' => 0, 'is_default' => true, 'sort_order' => 0, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Şeffaf Baskılı Sticker', 'slug' => 'transparent_sticker', 'extra_price' => 0, 'is_default' => false, 'sort_order' => 10, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Kilitli Torba Dönüşümü', 'slug' => 'zip_conversion', 'extra_price' => 0, 'is_default' => false, 'sort_order' => 20, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Kilitli Torba & Özel Sticker Seti', 'slug' => 'zip_sticker_set', 'extra_price' => 0, 'is_default' => false, 'sort_order' => 30, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (Schema::hasTable('interface_packaging_settings') && DB::table('interface_packaging_settings')->count() === 0) {
            DB::table('interface_packaging_settings')->insert([
                'barcode_enabled' => true,
                'barcode_label' => 'Barkod / Etiket Alanı İstiyorum',
                'barcode_extra_price' => 0,
                'barcode_description' => 'OPP poşet görselindeki barkod/etiket alanı talebi.',
                'barcode_image_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
};
