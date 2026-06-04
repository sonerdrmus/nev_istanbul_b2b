<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interface_packaging_preference_variations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price_multiplier', 12, 4)->default(1);
            $table->string('image_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        if (Schema::hasTable('product_variation_options')
            && ! Schema::hasColumn('product_variation_options', 'interface_packaging_preference_variation_id')) {
            Schema::table('product_variation_options', function (Blueprint $table) {
                $table->unsignedBigInteger('interface_packaging_preference_variation_id')
                    ->nullable()
                    ->after('interface_label_type_variation_id');

                $table->foreign(
                    'interface_packaging_preference_variation_id',
                    'pvo_interface_packaging_pref_var_id_fk'
                )
                    ->references('id')
                    ->on('interface_packaging_preference_variations')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('product_variation_options')
            && Schema::hasColumn('product_variation_options', 'interface_packaging_preference_variation_id')) {
            Schema::table('product_variation_options', function (Blueprint $table) {
                $table->dropForeign('pvo_interface_packaging_pref_var_id_fk');
                $table->dropColumn('interface_packaging_preference_variation_id');
            });
        }

        Schema::dropIfExists('interface_packaging_preference_variations');
    }
};
