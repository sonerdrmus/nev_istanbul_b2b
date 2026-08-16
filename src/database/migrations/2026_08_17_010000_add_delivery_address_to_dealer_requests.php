<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dealer_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('dealer_requests', 'different_delivery_address')) {
                $table->boolean('different_delivery_address')->default(false)->after('country');
            }
            if (! Schema::hasColumn('dealer_requests', 'delivery_address_line_1')) {
                $table->string('delivery_address_line_1')->nullable()->after('different_delivery_address');
            }
            if (! Schema::hasColumn('dealer_requests', 'delivery_address_line_2')) {
                $table->string('delivery_address_line_2')->nullable()->after('delivery_address_line_1');
            }
            if (! Schema::hasColumn('dealer_requests', 'delivery_city')) {
                $table->string('delivery_city')->nullable()->after('delivery_address_line_2');
            }
            if (! Schema::hasColumn('dealer_requests', 'delivery_postcode')) {
                $table->string('delivery_postcode', 32)->nullable()->after('delivery_city');
            }
            if (! Schema::hasColumn('dealer_requests', 'delivery_country')) {
                $table->string('delivery_country', 120)->nullable()->after('delivery_postcode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dealer_requests', function (Blueprint $table) {
            $columns = array_values(array_filter([
                'different_delivery_address',
                'delivery_address_line_1',
                'delivery_address_line_2',
                'delivery_city',
                'delivery_postcode',
                'delivery_country',
            ], fn (string $col): bool => Schema::hasColumn('dealer_requests', $col)));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
