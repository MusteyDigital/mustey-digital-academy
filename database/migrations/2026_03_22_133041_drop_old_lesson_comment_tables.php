<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('lesson_comment_reports');
        Schema::dropIfExists('lesson_comment_votes');
        Schema::dropIfExists('lesson_comments');
    }

    public function down(): void
    {
        Schema::create('lesson_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('lesson_comments')->nullOnDelete();
            $table->text('comment');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_best_answer')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('lesson_comment_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained('lesson_comments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_upvote')->default(true);
            $table->timestamps();
        });

        Schema::create('lesson_comment_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained('lesson_comments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }
};
