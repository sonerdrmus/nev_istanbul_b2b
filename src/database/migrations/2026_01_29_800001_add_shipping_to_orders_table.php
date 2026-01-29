<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shipping_method_id')->nullable()->after('notes')->constrained('shipping_methods')->nullOnDelete();
            $table->decimal('shipping_cost', 12, 2)->default(0)->after('shipping_method_id')->comment('TL cinsinden kargo tutarı');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['shipping_method_id']);
            $table->dropColumn(['shipping_method_id', 'shipping_cost']);
        });
    }
};
