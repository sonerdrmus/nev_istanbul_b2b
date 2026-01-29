<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->default(0)->comment('TL cinsinden kargo ücreti');
            $table->decimal('free_shipping_min_amount', 12, 2)->nullable()->comment('Bu tutar ve üzeri siparişlerde ücretsiz kargo (TL)');
            $table->string('estimated_days')->nullable()->comment('Örn: 2-4 iş günü');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};
