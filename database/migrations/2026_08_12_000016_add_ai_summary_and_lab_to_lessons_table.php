<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $t) {
            if (!Schema::hasColumn('lessons', 'ai_summary')) {
                $t->text('ai_summary')->nullable()->after('content');
            }
            if (!Schema::hasColumn('lessons', 'file_url')) {
                $t->string('file_url')->nullable()->after('file_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $t) {
            if (Schema::hasColumn('lessons', 'ai_summary')) {
                $t->dropColumn('ai_summary');
            }
            if (Schema::hasColumn('lessons', 'file_url')) {
                $t->dropColumn('file_url');
            }
        });
    }
};
