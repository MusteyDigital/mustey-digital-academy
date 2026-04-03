<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            if (!Schema::hasColumn('quizzes', 'published')) {
                $table->boolean('published')->default(false);
            }

            if (!Schema::hasColumn('quizzes', 'attempt_limit')) {
                $table->unsignedInteger('attempt_limit')->nullable();
            }

            if (!Schema::hasColumn('quizzes', 'time_limit')) {
                $table->unsignedInteger('time_limit')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            if (Schema::hasColumn('quizzes', 'published')) {
                $table->dropColumn('published');
            }

            if (Schema::hasColumn('quizzes', 'attempt_limit')) {
                $table->dropColumn('attempt_limit');
            }

            if (Schema::hasColumn('quizzes', 'time_limit')) {
                $table->dropColumn('time_limit');
            }
        });
    }
};
