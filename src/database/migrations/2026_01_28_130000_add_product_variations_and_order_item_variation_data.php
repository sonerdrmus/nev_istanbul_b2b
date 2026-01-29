<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Renk, Beden vb.
            $table->string('type')->default('select'); // select, checkbox
            $table->json('options'); // ["Kırmızı", "Mavi"] veya ["Var", "Yok"]
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->json('variation_data')->nullable()->after('subtotal'); // {"Renk":"Kırmızı","Beden":"M"}
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variations');
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('variation_data');
        });
    }
};
