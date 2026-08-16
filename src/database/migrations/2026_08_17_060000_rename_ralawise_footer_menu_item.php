<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('footer_menu_items')
            ->whereIn('label', [
                'Ralawise worldwide distribution',
                'Dağıtım ağı',
            ])
            ->update([
                'label' => 'Worldwide B2B Delivery',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('footer_menu_items')
            ->where('label', 'Worldwide B2B Delivery')
            ->update([
                'label' => 'Ralawise worldwide distribution',
                'updated_at' => now(),
            ]);
    }
};
