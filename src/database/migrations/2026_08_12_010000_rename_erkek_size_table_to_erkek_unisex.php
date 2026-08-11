<?php

use App\Models\ProductVariationOption;
use App\Models\SizeTable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('size_tables')) {
            return;
        }

        $newName = 'Erkek/Unisex';
        $newTitle = 'Erkek/Unisex Beden Tablosu';

        $tables = SizeTable::query()
            ->where(function ($q): void {
                $q->where('slug', 'erkek')
                    ->orWhere('name', 'Erkek')
                    ->orWhere('title', 'Erkek Beden Tablosu')
                    ->orWhere('title', 'BEDEN TABLOSU (ERKEK)');
            })
            ->get();

        foreach ($tables as $table) {
            $table->forceFill([
                'name' => $newName,
                'title' => $newTitle,
            ])->saveQuietly();

            if (! Schema::hasTable('product_variation_options')
                || ! Schema::hasColumn('product_variation_options', 'size_table_id')) {
                continue;
            }

            ProductVariationOption::query()
                ->where('size_table_id', $table->id)
                ->update(['option_value' => $newTitle]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('size_tables')) {
            return;
        }

        $tables = SizeTable::query()
            ->where(function ($q): void {
                $q->where('slug', 'erkek')
                    ->orWhere('name', 'Erkek/Unisex');
            })
            ->get();

        foreach ($tables as $table) {
            $table->forceFill([
                'name' => 'Erkek',
                'title' => 'Erkek Beden Tablosu',
            ])->saveQuietly();

            if (! Schema::hasTable('product_variation_options')
                || ! Schema::hasColumn('product_variation_options', 'size_table_id')) {
                continue;
            }

            ProductVariationOption::query()
                ->where('size_table_id', $table->id)
                ->update(['option_value' => 'Erkek Beden Tablosu']);
        }
    }
};
