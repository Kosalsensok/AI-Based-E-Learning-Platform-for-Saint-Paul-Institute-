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
        // 1. Discussion Threads Table
        Schema::create('discussion_threads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('course_name')->default('C Programming');
            $table->string('module_name')->default('Module 3');
            $table->string('author_name');
            $table->string('author_role')->default('student');
            $table->integer('replies_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->string('status')->default('open'); // open, solved, locked
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_solved')->default(false);
            $table->boolean('is_hot')->default(false);
            $table->integer('toxic_score')->default(2); // percentage
            $table->json('tags')->nullable();
            $table->timestamps();
        });

        // 2. Discussion Comments Table
        Schema::create('discussion_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('thread_id');
            $table->string('author_name');
            $table->string('author_role')->default('student');
            $table->text('content');
            $table->integer('likes_count')->default(0);
            $table->boolean('is_best_answer')->default(false);
            $table->boolean('is_teacher_answer')->default(false);
            $table->integer('toxic_score')->default(2);
            $table->timestamps();
        });

        // 3. Student Questions (Q&A) Table
        Schema::create('student_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question_code')->unique();
            $table->text('question_text');
            $table->string('course_name');
            $table->string('chapter_name');
            $table->string('student_name');
            $table->string('student_year')->default('Year 2');
            $table->integer('views_count')->default(0);
            $table->string('status')->default('pending'); // pending, answered, solved
            $table->string('priority')->default('medium'); // low, medium, high
            $table->boolean('sla_overdue')->default(false);
            $table->text('ai_draft_answer')->nullable();
            $table->integer('ai_confidence')->default(90);
            $table->text('final_answer')->nullable();
            $table->string('answered_by')->nullable();
            $table->integer('helpful_votes')->default(0);
            $table->boolean('is_faq')->default(false);
            $table->timestamps();
        });

        // 4. Support Tickets Table
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code')->unique();
            $table->string('student_name');
            $table->string('student_email');
            $table->string('subject');
            $table->string('category')->default('General'); // Login, Payment, Content, Account
            $table->string('priority')->default('high'); // low, medium, high
            $table->string('assigned_to')->default('Admin User');
            $table->string('sla_time_left')->default('2h left');
            $table->boolean('is_overdue')->default(false);
            $table->string('status')->default('new'); // new, open, waiting, resolved, closed
            $table->text('message');
            $table->string('evidence_url')->nullable();
            $table->json('timeline')->nullable();
            $table->timestamps();
        });

        // 5. Reported Content Table
        Schema::create('reported_contents', function (Blueprint $table) {
            $table->id();
            $table->string('report_code')->unique();
            $table->string('reported_by');
            $table->string('reason'); // Spam, Harassment, Inappropriate, Cheating, Copyright
            $table->string('content_type')->default('comment'); // comment, thread, file
            $table->string('author_name');
            $table->text('comment_text');
            $table->integer('toxic_score')->default(85);
            $table->string('evidence_url')->nullable();
            $table->string('status')->default('pending'); // pending, reviewed, dismissed
            $table->string('decision')->nullable(); // keep, hide, delete, warn, suspend
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reported_contents');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('student_questions');
        Schema::dropIfExists('discussion_comments');
        Schema::dropIfExists('discussion_threads');
    }
};
