<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // If the table already exists, only add missing columns
        Schema::table('attendances', function (Blueprint $table) {
            // add course_id only if you didn't have it before
            if (!Schema::hasColumn('attendances', 'course_id')) {
                $table->foreignId('course_id')->constrained()->onDelete('cascade');
            }

            // make lesson_id nullable if it exists
            // (SQLite in tests supports changing only if doctrine/dbal is installed,
            // so the safest way is: ensure lesson_id is nullable in the main create migration)
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'course_id')) {
                $table->dropConstrainedForeignId('course_id');
            }
        });
    }
};
