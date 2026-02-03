<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')->constrained()->onDelete('cascade');

            // ✅ MUST be nullable for LIVE attendance
            $table->foreignId('lesson_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('status')->default('present');
            $table->timestamp('marked_at')->nullable();

            $table->timestamps();

            // Optional but recommended: stop duplicates (per course+lesson+user)
            $table->unique(['course_id', 'lesson_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
