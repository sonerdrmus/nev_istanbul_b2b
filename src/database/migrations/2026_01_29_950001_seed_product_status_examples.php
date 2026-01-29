<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Bazı ürünlere örnek ürün durumu atar: Stokta yok, Yakında gelecek.
     */
    public function up(): void
    {
        $ids = DB::table('products')->orderBy('id')->pluck('id')->values()->all();
        if (count($ids) < 2) {
            return;
        }
        // İlk 2 ürün: Stokta yok
        foreach (array_slice($ids, 0, 2) as $id) {
            DB::table('products')->where('id', $id)->update(['status' => 'stokta_yok']);
        }
        // Sonraki 2 ürün: Yakında gelecek (en az 4 ürün varsa)
        if (count($ids) >= 4) {
            foreach (array_slice($ids, 2, 2) as $id) {
                DB::table('products')->where('id', $id)->update(['status' => 'yakinda_gelecek']);
            }
        }
        // Geri kalanlar varsayılan 'satista' kalır
    }

    public function down(): void
    {
        DB::table('products')->whereIn('status', ['stokta_yok', 'yakinda_gelecek'])->update(['status' => 'satista']);
    }
};
