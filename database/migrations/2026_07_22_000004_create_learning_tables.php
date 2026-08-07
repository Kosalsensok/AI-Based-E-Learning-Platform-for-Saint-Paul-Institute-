<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->enum('status', ['pending_payment','active','completed','cancelled','expired'])->default('pending_payment');
            $t->timestamp('enrolled_at')->nullable();
            $t->timestamps();
            $t->unique(['student_id', 'course_id']);
        });

        Schema::create('lesson_progress', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $t->unsignedTinyInteger('percent')->default(0);
            $t->unsignedInteger('seconds_watched')->default(0);
            $t->timestamp('completed_at')->nullable();
            $t->timestamp('client_updated_at')->nullable();
            $t->timestamps();
            $t->unique(['user_id', 'lesson_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
        Schema::dropIfExists('enrollments');
    }
};
