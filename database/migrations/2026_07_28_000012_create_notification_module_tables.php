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
        // 1. Announcements Table
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title_kh');
            $table->string('title_en')->nullable();
            $table->text('body_kh');
            $table->text('body_en')->nullable();
            $table->string('audience_type')->default('everyone'); // everyone, students, teachers, custom
            $table->json('audience_filters')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->json('delivery_channels')->nullable(); // in_app, email, push, sms
            $table->timestamp('scheduled_at')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->integer('pin_days')->default(7);
            $table->boolean('require_ack')->default(false);
            $table->boolean('allow_comments')->default(false);
            $table->string('status')->default('sent'); // draft, scheduled, sent, cancelled
            $table->integer('sent_count')->default(0);
            $table->decimal('read_rate', 5, 2)->default(0.00);
            $table->json('attachments')->nullable();
            $table->timestamps();
        });

        // 2. Email Templates Table
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('trigger_event'); // student_register, payment_success, etc.
            $table->string('subject_kh');
            $table->string('subject_en')->nullable();
            $table->longText('html_body');
            $table->string('from_name')->default('E.LMS Education');
            $table->string('reply_to')->default('support@elms.com');
            $table->boolean('attach_pdf_receipt')->default(false);
            $table->string('send_delay')->default('immediate');
            $table->string('status')->default('active'); // active, inactive
            $table->integer('sent_count')->default(0);
            $table->decimal('open_rate', 5, 2)->default(0.00);
            $table->timestamps();
        });

        // 3. Push Notifications Table
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->string('type')->default('content'); // content, reminder, engagement, achievement
            $table->string('rich_media_url')->nullable();
            $table->string('btn1_text')->nullable();
            $table->string('btn1_deeplink')->nullable();
            $table->string('btn2_text')->nullable();
            $table->string('target_audience')->default('all_students');
            $table->string('timing_type')->default('immediate'); // immediate, smart_ai, scheduled
            $table->timestamp('scheduled_at')->nullable();
            $table->string('status')->default('sent');
            $table->integer('sent_count')->default(0);
            $table->decimal('open_rate', 5, 2)->default(0.00);
            $table->decimal('click_rate', 5, 2)->default(0.00);
            $table->timestamps();
        });

        // 4. Notification Automations (IF-THEN Rules) Table
        Schema::create('notification_automations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('trigger_event'); // inactivity, assignment_due, birthday, etc.
            $table->json('trigger_conditions')->nullable();
            $table->json('audience_filter')->nullable();
            $table->json('action_channels')->nullable(); // push, email, sms
            $table->string('push_message')->nullable();
            $table->unsignedBigInteger('email_template_id')->nullable();
            $table->string('timing')->default('19:00');
            $table->string('frequency')->default('repeat_3_days');
            $table->integer('max_sends')->default(3);
            $table->string('status')->default('active'); // active, paused
            $table->timestamps();
        });

        // 5. Notification Logs Table
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('notif_code')->unique();
            $table->string('channel'); // email, push, in_app, sms
            $table->string('type')->nullable(); // transactional, announcement, reminder
            $table->string('title');
            $table->string('recipient_name');
            $table->string('recipient_email')->nullable();
            $table->string('recipient_device')->nullable();
            $table->string('status')->default('delivered'); // queued, sent, delivered, opened, clicked, failed, bounced
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->string('failure_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // 6. SMTP Settings Table
        Schema::create('smtp_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('mailgun'); // gmail, sendgrid, mailgun, custom
            $table->string('host')->default('smtp.mailgun.org');
            $table->integer('port')->default(587);
            $table->string('encryption')->default('tls');
            $table->string('username')->default('postmaster@elms.com');
            $table->string('password')->default('encrypted_secret');
            $table->string('from_email')->default('noreply@elms.com');
            $table->integer('daily_limit')->default(10000);
            $table->integer('used_today')->default(1245);
            $table->string('status')->default('connected');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smtp_settings');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('notification_automations');
        Schema::dropIfExists('push_notifications');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('announcements');
    }
};
