<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_comments', function (Blueprint $table) {
            if (!Schema::hasColumn('lesson_comments', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('lesson_comments', 'type')) {
                $table->string('type')->default('general')->after('comment');
            }
            if (!Schema::hasColumn('lesson_comments', 'votes')) {
                $table->integer('votes')->default(0)->after('type');
            }
            if (!Schema::hasColumn('lesson_comments', 'is_best_answer')) {
                $table->boolean('is_best_answer')->default(false)->after('is_pinned');
            }
        });

        try {
            Schema::table('lesson_comments', function (Blueprint $table) {
                $table->foreign('parent_id')
                    ->references('id')
                    ->on('lesson_comments')
                    ->nullOnDelete();
            });
        } catch (\Throwable $e) {
            // ignore if constraint already exists
        }

        DB::statement("UPDATE lesson_comments SET type = 'general' WHERE type IS NULL");
        DB::statement("UPDATE lesson_comments SET votes = 0 WHERE votes IS NULL");
        DB::statement("UPDATE lesson_comments SET is_best_answer = false WHERE is_best_answer IS NULL");
    }

    public function down(): void
    {
        try {
            Schema::table('lesson_comments', function (Blueprint $table) {
                $table->dropForeign(['parent_id']);
            });
        } catch (\Throwable $e) {
            // ignore
        }

        Schema::table('lesson_comments', function (Blueprint $table) {
            foreach (['parent_id', 'type', 'votes', 'is_best_answer'] as $column) {
                if (Schema::hasColumn('lesson_comments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
