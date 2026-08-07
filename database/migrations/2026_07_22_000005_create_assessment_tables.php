<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->foreignId('module_id')->nullable()->constrained()->nullOnDelete();
            $t->string('title');
            $t->enum('type', ['pre_test', 'practice', 'post_test', 'assignment'])->default('practice');
            $t->unsignedInteger('time_limit_minutes')->nullable();
            $t->unsignedTinyInteger('passing_score')->default(50);
            $t->unsignedTinyInteger('max_attempts')->default(3);
            $t->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $t->timestamps();
        });

        Schema::create('questions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $t->enum('type', ['mcq', 'true_false', 'fill_blank', 'essay'])->default('mcq');
            $t->text('question');
            $t->json('options')->nullable();
            $t->json('correct_answer')->nullable();
            $t->unsignedInteger('points')->default(1);
            $t->timestamps();
        });

        Schema::create('quiz_attempts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $t->json('answers');
            $t->decimal('score', 5, 2)->nullable();
            $t->boolean('passed')->default(false);
            $t->unsignedTinyInteger('attempt_number');
            $t->string('client_uuid')->nullable()->unique();
            $t->timestamp('started_at');
            $t->timestamp('submitted_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('quizzes');
    }
};
