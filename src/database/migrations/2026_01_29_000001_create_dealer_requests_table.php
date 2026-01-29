<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dealer_requests', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('tc_no', 11);
            $table->string('email');
            $table->string('phone', 32)->nullable();
            $table->text('address')->nullable();

            $table->string('document_pdf_path')->nullable();
            $table->string('document_jpeg_path')->nullable();

            $table->string('status')->default('pending'); // pending|approved|rejected
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('created_company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('created_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->unique('email');
            $table->unique('tc_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dealer_requests');
    }
};

