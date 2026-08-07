<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Extend modules table for Enterprise LMS
        Schema::table('modules', function (Blueprint $t) {
            if (!Schema::hasColumn('modules', 'kh_title')) {
                $t->string('kh_title')->nullable()->after('title');
            }
            if (!Schema::hasColumn('modules', 'kh_description')) {
                $t->text('kh_description')->nullable()->after('kh_title');
            }
            if (!Schema::hasColumn('modules', 'description')) {
                $t->text('description')->nullable();
            }
            if (!Schema::hasColumn('modules', 'learning_objectives')) {
                $t->json('learning_objectives')->nullable();
            }
            if (!Schema::hasColumn('modules', 'estimated_duration')) {
                $t->string('estimated_duration')->default('3 Hours');
            }
        });

        // 2. Create chapters table
        if (!Schema::hasTable('chapters')) {
            Schema::create('chapters', function (Blueprint $t) {
                $t->id();
                $t->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
                $t->string('chapter_number')->default('1.1');
                $t->string('title');
                $t->string('kh_title')->nullable();
                $t->unsignedInteger('order')->default(0);
                $t->timestamps();
            });
        }

        // 3. Extend lessons table for Chapter mapping & Multilingual Subtitles
        Schema::table('lessons', function (Blueprint $t) {
            if (!Schema::hasColumn('lessons', 'chapter_id')) {
                $t->foreignId('chapter_id')->nullable()->constrained('chapters')->nullOnDelete();
            }
            if (!Schema::hasColumn('lessons', 'kh_title')) {
                $t->string('kh_title')->nullable()->after('title');
            }
            if (!Schema::hasColumn('lessons', 'is_linked')) {
                $t->boolean('is_linked')->default(false);
            }
            if (!Schema::hasColumn('lessons', 'has_kh')) {
                $t->boolean('has_kh')->default(true);
            }
            if (!Schema::hasColumn('lessons', 'has_en')) {
                $t->boolean('has_en')->default(true);
            }
            if (!Schema::hasColumn('lessons', 'has_cc')) {
                $t->boolean('has_cc')->default(true);
            }
        });

        // 4. Create VTT Subtitles table for AI translation
        if (!Schema::hasTable('vtt_subtitles')) {
            Schema::create('vtt_subtitles', function (Blueprint $t) {
                $t->id();
                $t->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
                $t->string('language_code', 10)->default('kh'); // 'kh', 'en'
                $t->longText('vtt_content')->nullable();
                $t->json('cues_data')->nullable();
                $t->boolean('is_ai_translated')->default(false);
                $t->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vtt_subtitles');
        Schema::dropIfExists('chapters');
    }
};
