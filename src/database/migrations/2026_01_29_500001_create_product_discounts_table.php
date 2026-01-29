<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_group_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1)->comment('Minimum adet');
            $table->unsignedTinyInteger('priority')->default(1)->comment('Öncelik (küçük = önce)');
            $table->decimal('price', 12, 4)->comment('İndirimli birim fiyat');
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_discounts');
    }
};
