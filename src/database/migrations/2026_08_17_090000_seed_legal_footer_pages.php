<?php

use Database\Seeders\LegalPageSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        (new LegalPageSeeder)->seedLegalFooterPages();
    }

    public function down(): void
    {
        // Content seed only.
    }
};
