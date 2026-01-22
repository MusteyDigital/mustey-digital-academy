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
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['present'])->default('present');
            $table->timestamp('marked_at')->nullable();
            $table->timestamps();

            $table->unique(['lesson_id', 'user_id']); // prevent double marking
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
