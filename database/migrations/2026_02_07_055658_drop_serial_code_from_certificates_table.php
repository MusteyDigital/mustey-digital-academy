<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Postgres: drop UNIQUE CONSTRAINT (not index)
        DB::statement('ALTER TABLE certificates DROP CONSTRAINT IF EXISTS certificates_serial_code_unique');

        // Now drop the column safely
        if (Schema::hasColumn('certificates', 'serial_code')) {
            Schema::table('certificates', function ($table) {
                $table->dropColumn('serial_code');
            });
        }
    }

    public function down(): void
    {
        // Optional: restore column
        if (!Schema::hasColumn('certificates', 'serial_code')) {
            Schema::table('certificates', function ($table) {
                $table->string('serial_code')->nullable();
            });
        }

        // Optional: restore constraint (only if you really need it)
        DB::statement('ALTER TABLE certificates ADD CONSTRAINT certificates_serial_code_unique UNIQUE (serial_code)');
    }
};
