<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('serial')->unique();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // student
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'user_id']); // one cert per student per course
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
