<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            if (!Schema::hasColumn('users', 'name_kh')) {
                $t->string('name_kh')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'student_code')) {
                $t->string('student_code', 50)->nullable()->unique()->after('major_id');
            }
            if (!Schema::hasColumn('users', 'study_type')) {
                $t->string('study_type', 30)->default('on_campus')->after('student_code');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $t->string('status', 30)->default('active')->after('study_type');
            }
            if (!Schema::hasColumn('users', 'qualification')) {
                $t->string('qualification')->nullable()->after('status');
            }
            if (!Schema::hasColumn('users', 'expertise')) {
                $t->text('expertise')->nullable()->after('qualification');
            }
            if (!Schema::hasColumn('users', 'login_attempts')) {
                $t->integer('login_attempts')->default(0)->after('expertise');
            }
            if (!Schema::hasColumn('users', 'locked_until')) {
                $t->timestamp('locked_until')->nullable()->after('login_attempts');
            }
        });

        if (!Schema::hasTable('auth_logs')) {
            Schema::create('auth_logs', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $t->string('email')->index();
                $t->string('ip_address', 45)->nullable();
                $t->text('user_agent')->nullable();
                $t->string('device')->nullable();
                $t->string('browser')->nullable();
                $t->string('status', 30)->default('success'); // success, failed, locked
                $t->string('location')->nullable();
                $t->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_logs');
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn([
                'name_kh',
                'student_code',
                'study_type',
                'status',
                'qualification',
                'expertise',
                'login_attempts',
                'locked_until',
            ]);
        });
    }
};
