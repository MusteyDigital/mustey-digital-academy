<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('title');
            $table->unsignedInteger('time_limit_minutes')->nullable()->after('is_published');
            $table->unsignedInteger('max_attempts')->nullable()->after('time_limit_minutes');
            $table->unsignedInteger('pass_mark')->nullable()->after('max_attempts'); // 0-100
            $table->timestamp('opens_at')->nullable()->after('pass_mark');
            $table->timestamp('closes_at')->nullable()->after('opens_at');
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn([
                'is_published',
                'time_limit_minutes',
                'max_attempts',
                'pass_mark',
                'opens_at',
                'closes_at',
            ]);
        });
    }
};
