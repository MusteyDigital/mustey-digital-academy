<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drab_attempts', function (Blueprint $table) {
            $table->string('difficulty', 20)->default('easy')->after('lesson_id');
        });
    }

    public function down(): void
    {
        Schema::table('drab_attempts', function (Blueprint $table) {
            $table->dropColumn('difficulty');
        });
    }
};
