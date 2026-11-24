<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('media_storage_settings')) {
            return;
        }

        Schema::create('media_storage_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(false);
            $table->string('provider', 32)->default('r2'); // r2 | s3
            $table->string('name', 255)->nullable(); // bucket adı (görünen)
            $table->string('location', 255)->nullable(); // örn. Eastern Europe (EEUR)
            $table->string('bucket', 255)->nullable();
            $table->string('endpoint', 500)->nullable();
            $table->string('region', 64)->nullable()->default('auto');
            $table->string('access_key_id', 255)->nullable();
            $table->text('secret_access_key')->nullable(); // encrypted cast
            $table->string('public_url', 500)->nullable(); // tarayıcı CDN / custom domain
            $table->boolean('use_path_style_endpoint')->default(true);
            $table->boolean('use_for_uploads')->default(false); // public disk → S3
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_storage_settings');
    }
};
