<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('quiz_attempts', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('quiz_attempts', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('started_at');
            }

            if (!Schema::hasColumn('quiz_attempts', 'created_at') && !Schema::hasColumn('quiz_attempts', 'updated_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('quiz_attempts', 'started_at')) {
                $table->dropColumn('started_at');
            }

            if (Schema::hasColumn('quiz_attempts', 'submitted_at')) {
                $table->dropColumn('submitted_at');
            }

            if (Schema::hasColumn('quiz_attempts', 'created_at') && Schema::hasColumn('quiz_attempts', 'updated_at')) {
                $table->dropTimestamps();
            }
        });
    }
};
