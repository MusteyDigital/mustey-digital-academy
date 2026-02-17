<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Increase verify_token length to 64
        DB::statement('ALTER TABLE certificates ALTER COLUMN verify_token TYPE VARCHAR(64)');
    }

    public function down(): void
    {
        // Revert back to 60 if ever needed
        DB::statement('ALTER TABLE certificates ALTER COLUMN verify_token TYPE VARCHAR(60)');
    }
};
