<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(false);
            $table->string('mailer', 32)->default('smtp');
            $table->string('host')->nullable();
            $table->unsignedInteger('port')->nullable();
            $table->string('scheme', 16)->nullable(); // tls | ssl | null
            $table->string('username')->nullable();
            $table->text('password')->nullable(); // encrypted cast
            $table->string('from_address')->nullable();
            $table->string('from_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_settings');
    }
};
