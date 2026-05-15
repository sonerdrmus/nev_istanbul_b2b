<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** MySQL identifier limit is 64 chars; Laravel's default FK name exceeds it. */
    private const FK_INTERFACE_FABRIC_TYPE_VARIATION = 'pvo_iface_fabric_type_var_id_fk';

    public function up(): void
    {
        if (Schema::hasColumn('product_variation_options', 'interface_fabric_type_variation_id')) {
            return;
        }

        Schema::table('product_variation_options', function (Blueprint $table) {
            $table->unsignedBigInteger('interface_fabric_type_variation_id')
                ->nullable()
                ->after('interface_color_variation_id');

            $table->foreign('interface_fabric_type_variation_id', self::FK_INTERFACE_FABRIC_TYPE_VARIATION)
                ->references('id')
                ->on('interface_fabric_type_variations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('product_variation_options', 'interface_fabric_type_variation_id')) {
            return;
        }

        Schema::table('product_variation_options', function (Blueprint $table) {
            $table->dropForeign(self::FK_INTERFACE_FABRIC_TYPE_VARIATION);
            $table->dropColumn('interface_fabric_type_variation_id');
        });
    }
};
