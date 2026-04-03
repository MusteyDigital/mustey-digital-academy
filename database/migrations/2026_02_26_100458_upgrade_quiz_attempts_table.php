<?php

// database/migrations/2026_02_26_100458_upgrade_quiz_attempts_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('quiz_attempts', function (Blueprint $table) {
      $table->timestamp('started_at')->nullable()->after('user_id');
      $table->timestamp('submitted_at')->nullable()->after('started_at');
      $table->enum('status', ['in_progress', 'submitted'])->default('submitted')->after('submitted_at');
      $table->unsignedInteger('percentage')->nullable()->after('total');
    });
  }

  public function down(): void {
    Schema::table('quiz_attempts', function (Blueprint $table) {
      $table->dropColumn(['started_at','submitted_at','status','percentage']);
    });
  }
};
