<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_attempt_answers', function (Blueprint $table) {
            if (!Schema::hasColumn('quiz_attempt_answers', 'selected')) {
                // store the chosen option like: a/b/c/d
                $table->string('selected', 5)->nullable()->after('question_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quiz_attempt_answers', function (Blueprint $table) {
            if (Schema::hasColumn('quiz_attempt_answers', 'selected')) {
                $table->dropColumn('selected');
            }
        });
    }
};
