<?php

use Database\Seeders\LegalPageSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        (new LegalPageSeeder)->run();
    }

    public function down(): void
    {
        // Content seed only; do not drop legal_pages (owned by create_legal_pages_table).
    }
};
