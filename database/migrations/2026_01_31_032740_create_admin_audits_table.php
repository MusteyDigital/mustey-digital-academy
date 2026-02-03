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
    Schema::create('admin_audits', function (Blueprint $table) {
        $table->id();
        $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
        $table->string('action'); // e.g. role_updated
        $table->string('target_type')->nullable(); // e.g. User
        $table->unsignedBigInteger('target_id')->nullable(); // user id
        $table->json('meta')->nullable(); // store extra info
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_audits');
    }
};
