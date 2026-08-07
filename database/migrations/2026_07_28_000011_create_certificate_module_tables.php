<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Certificate Templates Table
        Schema::create('certificate_templates', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('assigned_type')->default('all'); // all, major, course
            $t->unsignedBigInteger('assigned_id')->nullable();
            $t->string('language')->default('kh_en'); // kh_en, km, en
            $t->string('paper_size')->default('a4_landscape');
            $t->string('background_image')->nullable();
            $t->string('status')->default('active'); // active, draft
            $t->json('elements')->nullable();
            $t->timestamps();
        });

        // 2. Add columns to Certificates Table if missing
        Schema::table('certificates', function (Blueprint $t) {
            if (!Schema::hasColumn('certificates', 'major_id')) {
                $t->foreignId('major_id')->nullable()->constrained('majors')->nullOnDelete();
            }
            if (!Schema::hasColumn('certificates', 'template_id')) {
                $t->foreignId('template_id')->nullable()->constrained('certificate_templates')->nullOnDelete();
            }
            if (!Schema::hasColumn('certificates', 'grade')) {
                $t->string('grade')->nullable()->default('A');
            }
            if (!Schema::hasColumn('certificates', 'score')) {
                $t->integer('score')->nullable()->default(85);
            }
            if (!Schema::hasColumn('certificates', 'status')) {
                $t->string('status')->default('valid'); // valid, revoked, pending
            }
            if (!Schema::hasColumn('certificates', 'revoked_at')) {
                $t->timestamp('revoked_at')->nullable();
            }
            if (!Schema::hasColumn('certificates', 'revoked_by')) {
                $t->foreignId('revoked_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('certificates', 'revocation_reason')) {
                $t->string('revocation_reason')->nullable();
            }
            if (!Schema::hasColumn('certificates', 'revocation_evidence')) {
                $t->string('revocation_evidence')->nullable();
            }
            if (!Schema::hasColumn('certificates', 'revocation_note')) {
                $t->text('revocation_note')->nullable();
            }
            if (!Schema::hasColumn('certificates', 'show_reason_publicly')) {
                $t->boolean('show_reason_publicly')->default(true);
            }
            if (!Schema::hasColumn('certificates', 'downloads_count')) {
                $t->integer('downloads_count')->default(0);
            }
            if (!Schema::hasColumn('certificates', 'verifications_count')) {
                $t->integer('verifications_count')->default(0);
            }
            if (!Schema::hasColumn('certificates', 'last_verified_at')) {
                $t->timestamp('last_verified_at')->nullable();
            }
            if (!Schema::hasColumn('certificates', 'file_path')) {
                $t->string('file_path')->nullable();
            }
            if (!Schema::hasColumn('certificates', 'audit_trail')) {
                $t->json('audit_trail')->nullable();
            }
        });

        // 3. Verification Activity Logs Table
        Schema::create('certificate_verification_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('certificate_id')->nullable()->constrained('certificates')->cascadeOnDelete();
            $t->string('certificate_number')->index();
            $t->string('result')->default('valid'); // valid, revoked, not_found, pending
            $t->string('ip_address')->nullable();
            $t->string('location')->nullable();
            $t->string('source')->default('manual_id'); // qr_scan, manual_id, admin_tool
            $t->timestamps();
        });

        // 4. Auto Rules Table
        Schema::create('certificate_auto_rules', function (Blueprint $t) {
            $t->id();
            $t->boolean('auto_issue_enabled')->default(true);
            $t->boolean('require_progress_100')->default(true);
            $t->boolean('require_post_test_passed')->default(true);
            $t->boolean('require_assignments_passed')->default(true);
            $t->boolean('require_payment_verified')->default(true);
            $t->boolean('require_no_violations')->default(true);
            $t->unsignedBigInteger('default_template_id')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_auto_rules');
        Schema::dropIfExists('certificate_verification_logs');
        Schema::dropIfExists('certificate_templates');
    }
};
