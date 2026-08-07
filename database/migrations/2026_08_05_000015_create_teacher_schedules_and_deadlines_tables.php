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
        Schema::create('teacher_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('set null');
            $table->foreignId('module_id')->nullable()->constrained('modules')->onDelete('set null');
            $table->foreignId('major_id')->nullable()->constrained('majors')->onDelete('set null');
            $table->string('type')->default('live_class'); // live_class, qa_session, office_hour, exam
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('learning_mode')->default('instructor_led'); // instructor_led, self_paced
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('timezone')->default('Asia/Phnom_Penh');
            $table->string('repeat_rule')->default('none'); // none, weekly, custom
            $table->string('location_type')->default('online'); // online, room
            $table->string('room_number')->nullable();
            $table->text('meeting_link')->nullable();
            $table->integer('capacity')->default(60);
            $table->boolean('notify_email')->default(true);
            $table->boolean('notify_push')->default(true);
            $table->boolean('notify_announcement')->default(true);
            $table->boolean('reminder_15m')->default(true);
            $table->boolean('reminder_1h')->default(true);
            $table->boolean('auto_record')->default(true);
            $table->string('status')->default('upcoming'); // upcoming, live, completed, cancelled
            $table->timestamps();
        });

        Schema::create('deadlines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('set null');
            $table->string('linked_type')->default('quiz'); // quiz, assignment, module, payment
            $table->unsignedBigInteger('linked_id')->nullable();
            $table->string('title');
            $table->string('deadline_type')->default('hard'); // hard, soft
            $table->dateTime('due_at');
            $table->integer('grace_days')->default(0);
            $table->integer('penalty_percent')->default(0);
            $table->string('applicable_to')->default('all'); // all, instructor_led_only, unsubmitted_only
            $table->boolean('show_countdown')->default(true);
            $table->boolean('auto_reminder_24h')->default(true);
            $table->boolean('auto_reminder_1h')->default(true);
            $table->boolean('auto_lock')->default(true);
            $table->text('message_kh')->nullable();
            $table->text('message_en')->nullable();
            $table->string('status')->default('active'); // active, extended, closed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deadlines');
        Schema::dropIfExists('teacher_schedules');
    }
};
