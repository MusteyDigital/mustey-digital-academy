<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('courses', function (Blueprint $table) {
        if (!Schema::hasColumn('courses', 'meeting_url')) {
            $table->string('meeting_url')->nullable()->after('description');
        }

        if (!Schema::hasColumn('courses', 'starts_at')) {
            $table->dateTime('starts_at')->nullable()->after('meeting_url');
        }
    });
}
    public function down(): void
{
    Schema::table('courses', function (Blueprint $table) {
        if (Schema::hasColumn('courses', 'starts_at')) {
            $table->dropColumn('starts_at');
        }

        if (Schema::hasColumn('courses', 'meeting_url')) {
            $table->dropColumn('meeting_url');
        }
    });
}
};
