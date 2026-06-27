<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            // Drop foreign keys that depend on the unique index first
            $table->dropForeign(['quiz_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            // Now safe to drop the unique index
            $table->dropUnique(['quiz_id', 'user_id']);
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            // Recreate foreign keys without the unique constraint
            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropForeign(['quiz_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->unique(['quiz_id', 'user_id']);
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
