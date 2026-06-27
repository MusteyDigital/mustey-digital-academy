<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('certificates', 'serial_code')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropUnique('certificates_serial_code_unique');
                $table->dropColumn('serial_code');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('certificates', 'serial_code')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->string('serial_code')->unique()->nullable();
            });
        }
    }
};
