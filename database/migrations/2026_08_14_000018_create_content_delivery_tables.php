<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. course_videos Table
        if (!Schema::hasTable('course_videos')) {
            Schema::create('course_videos', function (Blueprint $t) {
                $t->id();
                $t->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $t->foreignId('lesson_id')->nullable()->constrained('lessons')->nullOnDelete();
                $t->string('title');
                $t->string('cloudinary_url')->nullable();
                $t->string('video_path')->nullable();
                $t->string('duration')->nullable();
                $t->unsignedInteger('duration_seconds')->default(0);
                $t->string('file_size')->nullable();
                $t->enum('status', ['uploading', 'processing', 'ready', 'failed'])->default('ready');
                $t->unsignedInteger('transcode_progress')->default(100);
                $t->timestamps();
            });
        }

        // 2. course_materials Table (PDFs, Slides, Notes/Downloads)
        if (!Schema::hasTable('course_materials')) {
            Schema::create('course_materials', function (Blueprint $t) {
                $t->id();
                $t->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $t->foreignId('lesson_id')->nullable()->constrained('lessons')->nullOnDelete();
                $t->string('title');
                $t->string('type')->default('pdf'); // 'pdf', 'slide', 'note_download', 'download'
                $t->string('file_url')->nullable();
                $t->string('file_path')->nullable();
                $t->string('file_size')->nullable();
                $t->string('file_name')->nullable();
                $t->unsignedInteger('slide_count')->nullable();
                $t->unsignedInteger('download_count')->default(0);
                $t->boolean('self_paced_only')->default(false);
                $t->string('status')->default('ready');
                $t->timestamps();
            });
        }

        // 3. ai_generated_content Table (Quiz, Summary, Flashcards drafts)
        if (!Schema::hasTable('ai_generated_content')) {
            Schema::create('ai_generated_content', function (Blueprint $t) {
                $t->id();
                $t->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $t->foreignId('lesson_id')->nullable()->constrained('lessons')->nullOnDelete();
                $t->enum('type', ['quiz', 'summary', 'flashcards'])->default('quiz');
                $t->string('title')->nullable();
                $t->json('content_json')->nullable();
                $t->enum('status', ['draft', 'approved', 'rejected'])->default('draft');
                $t->dateTime('approved_at')->nullable();
                $t->timestamps();
            });
        }

        // 4. lab_integrations Table (Network simulator, Code sandbox, Virtual machine)
        if (!Schema::hasTable('lab_integrations')) {
            Schema::create('lab_integrations', function (Blueprint $t) {
                $t->id();
                $t->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $t->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
                $t->string('title')->nullable();
                $t->enum('lab_type', ['network_simulator', 'code_sandbox', 'virtual_machine'])->default('code_sandbox');
                $t->json('config_json')->nullable();
                $t->string('provider_url')->nullable();
                $t->string('status')->default('active');
                $t->timestamps();
            });
        }

        // 5. Add optional content_ref_id or order_index aliases if needed
        Schema::table('lessons', function (Blueprint $t) {
            if (!Schema::hasColumn('lessons', 'content_ref_id')) {
                $t->unsignedBigInteger('content_ref_id')->nullable()->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_integrations');
        Schema::dropIfExists('ai_generated_content');
        Schema::dropIfExists('course_materials');
        Schema::dropIfExists('course_videos');

        Schema::table('lessons', function (Blueprint $t) {
            if (Schema::hasColumn('lessons', 'content_ref_id')) {
                $t->dropColumn('content_ref_id');
            }
        });
    }
};
