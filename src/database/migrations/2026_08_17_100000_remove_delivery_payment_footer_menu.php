<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $groupIds = DB::table('footer_menu_groups')
            ->whereIn('title', [
                'Teslimat & Ödeme',
                'Teslimat & Odeme',
                'Teslimat ve Ödeme',
                'Delivery & Payment',
                'Delivery and Payment',
                'Consegna e pagamento',
            ])
            ->pluck('id');

        if ($groupIds->isEmpty()) {
            return;
        }

        DB::table('footer_menu_groups')->whereIn('id', $groupIds)->delete();
    }

    public function down(): void
    {
        $exists = DB::table('footer_menu_groups')->where('title', 'Teslimat & Ödeme')->exists();
        if ($exists) {
            return;
        }

        $id = DB::table('footer_menu_groups')->insertGetId([
            'title' => 'Teslimat & Ödeme',
            'type' => 'menu',
            'sort_order' => 20,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $now = now();
        DB::table('footer_menu_items')->insert([
            ['footer_menu_group_id' => $id, 'label' => 'Teslimat bilgisi ve maliyetler', 'url' => '#', 'open_in_new_tab' => false, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['footer_menu_group_id' => $id, 'label' => 'Faturasız ödeme', 'url' => '#', 'open_in_new_tab' => false, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['footer_menu_group_id' => $id, 'label' => 'İade', 'url' => '#', 'open_in_new_tab' => false, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['footer_menu_group_id' => $id, 'label' => 'Worldwide B2B Delivery', 'url' => '#', 'open_in_new_tab' => false, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
};
