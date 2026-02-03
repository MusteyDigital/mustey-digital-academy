<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {

            if (!Schema::hasColumn('certificates', 'serial_code')) {
                $table->string('serial_code')->unique()->after('course_id');
            }

            if (!Schema::hasColumn('certificates', 'verify_token')) {
                $table->string('verify_token', 60)->unique()->after('serial_code');
            }

            if (!Schema::hasColumn('certificates', 'issued_at')) {
                $table->timestamp('issued_at')->nullable()->after('verify_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {

            if (Schema::hasColumn('certificates', 'serial_code')) {
                $table->dropColumn('serial_code');
            }

            if (Schema::hasColumn('certificates', 'verify_token')) {
                $table->dropColumn('verify_token');
            }

            if (Schema::hasColumn('certificates', 'issued_at')) {
                $table->dropColumn('issued_at');
            }
        });
    }
};
