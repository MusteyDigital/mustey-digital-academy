<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('lessons', function (\Illuminate\Database\Schema\Blueprint $table) {
        $table->id();
        $table->foreignId('course_id')->constrained()->cascadeOnDelete();
        $table->string('title');
        $table->longText('content')->nullable(); // text lesson notes
        $table->string('video_url')->nullable(); // youtube/google drive/video link
        $table->timestamp('starts_at')->nullable(); // for live class timing
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('lessons');
}

};
