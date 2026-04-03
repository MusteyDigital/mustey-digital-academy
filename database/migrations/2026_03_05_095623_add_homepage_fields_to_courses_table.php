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
    Schema::table('courses', function (Blueprint $table) {
        // thumbnail already exists ✅ (do not add again)

        $table->string('level')->nullable();
        $table->string('duration')->nullable();
        $table->boolean('is_featured')->default(false);
        $table->string('category')->nullable();
    });
}

public function down(): void
{
    Schema::table('courses', function (Blueprint $table) {
        $table->dropColumn(['level', 'duration', 'is_featured', 'category']);
    });
}

};
