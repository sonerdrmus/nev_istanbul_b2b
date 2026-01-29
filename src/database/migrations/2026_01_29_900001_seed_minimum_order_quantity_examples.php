<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Mevcut ürünlerin ilk birkaçına örnek minimum sipariş miktarı atar.
     */
    public function up(): void
    {
        $examples = [5, 10, 12, 6, 20]; // örnek min. miktarlar
        $ids = DB::table('products')->orderBy('id')->limit(count($examples))->pluck('id');
        foreach ($ids->values()->all() as $i => $id) {
            $min = $examples[$i] ?? 1;
            DB::table('products')->where('id', $id)->update(['minimum_order_quantity' => $min]);
        }
    }

    public function down(): void
    {
        $ids = DB::table('products')->orderBy('id')->limit(5)->pluck('id');
        if ($ids->isNotEmpty()) {
            DB::table('products')->whereIn('id', $ids)->update(['minimum_order_quantity' => null]);
        }
    }
};
