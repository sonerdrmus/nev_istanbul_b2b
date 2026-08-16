<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $groupIds = DB::table('footer_menu_groups')
            ->whereIn('title', ['Şirket', 'Company', 'Azienda'])
            ->pluck('id');

        if ($groupIds->isEmpty()) {
            return;
        }

        DB::table('footer_menu_items')
            ->whereIn('footer_menu_group_id', $groupIds)
            ->whereIn('label', [
                'Kullanım Koşulları',
                'Terms of Use',
                'Terms of use',
                "Condizioni d'uso",
                'Condizioni di utilizzo',
            ])
            ->delete();
    }

    public function down(): void
    {
        $group = DB::table('footer_menu_groups')->where('title', 'Şirket')->first();

        if (! $group) {
            return;
        }

        $exists = DB::table('footer_menu_items')
            ->where('footer_menu_group_id', $group->id)
            ->where('label', 'Kullanım Koşulları')
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('footer_menu_items')->insert([
            'footer_menu_group_id' => $group->id,
            'label' => 'Kullanım Koşulları',
            'url' => '#',
            'open_in_new_tab' => false,
            'sort_order' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
