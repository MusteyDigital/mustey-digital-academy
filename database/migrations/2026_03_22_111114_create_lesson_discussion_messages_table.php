<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_discussion_messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('lesson_discussion_messages')
                ->nullOnDelete();

            $table->text('body');

            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_answer')->default(false);

            $table->timestamp('edited_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['lesson_id', 'created_at']);
            $table->index(['course_id', 'created_at']);
            $table->index(['parent_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_discussion_messages');
    }
};
