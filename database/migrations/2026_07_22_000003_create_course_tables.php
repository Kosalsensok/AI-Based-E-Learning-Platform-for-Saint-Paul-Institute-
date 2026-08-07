<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('major_id')->constrained()->cascadeOnDelete();
            $t->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $t->string('title');
            $t->string('code')->unique();
            $t->text('description')->nullable();
            $t->enum('learning_mode', ['instructor_led', 'self_paced'])->default('self_paced');
            $t->boolean('is_paid')->default(false);
            $t->decimal('price', 8, 2)->default(0);
            $t->enum('status', ['draft','pending','published','rejected','archived'])->default('draft');
            $t->string('thumbnail')->nullable();
            $t->timestamps();
        });

        Schema::create('modules', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->unsignedInteger('order')->default(0);
            $t->timestamps();
        });

        Schema::create('lessons', function (Blueprint $t) {
            $t->id();
            $t->foreignId('module_id')->constrained()->cascadeOnDelete();
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->enum('type', ['video', 'pdf', 'slides', 'notes'])->default('video');
            $t->string('file_path')->nullable();
            $t->string('video_url')->nullable();
            $t->text('content')->nullable();
            $t->unsignedInteger('duration_seconds')->default(0);
            $t->unsignedInteger('order')->default(0);
            $t->boolean('is_free_preview')->default(false);
            $t->boolean('downloadable')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('courses');
    }
};
