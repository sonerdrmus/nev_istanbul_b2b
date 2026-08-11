<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('size_tables')) {
            return;
        }

        DB::table('size_tables')
            ->where(function ($q): void {
                $q->where('slug', 'erkek')
                    ->orWhere('name', 'Erkek/Unisex')
                    ->orWhere('name', 'Erkek');
            })
            ->where(function ($q): void {
                $q->whereNull('trigger_option_value')
                    ->orWhere('trigger_option_value', '')
                    ->orWhere('trigger_option_value', 'Erkek');
            })
            ->update([
                'trigger_option_value' => 'Erkek|Unisex',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('size_tables')) {
            return;
        }

        DB::table('size_tables')
            ->where('trigger_option_value', 'Erkek|Unisex')
            ->update([
                'trigger_option_value' => 'Erkek',
                'updated_at' => now(),
            ]);
    }
};
