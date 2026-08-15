<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('thumbnail');
            }
            if (!Schema::hasColumn('courses', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('submitted_at');
            }
            if (!Schema::hasColumn('courses', 'rejection_note')) {
                $table->text('rejection_note')->nullable()->after('reviewed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'rejection_note')) {
                $table->dropColumn('rejection_note');
            }
            if (Schema::hasColumn('courses', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }
            if (Schema::hasColumn('courses', 'submitted_at')) {
                $table->dropColumn('submitted_at');
            }
        });
    }
};
