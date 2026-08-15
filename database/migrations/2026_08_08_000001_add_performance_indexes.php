<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ai_recommendations')) {
            Schema::table('ai_recommendations', function (Blueprint $table) {
                $table->index('user_id', 'idx_ai_rec_user_id');
                $table->index('type', 'idx_ai_rec_type');
                $table->index('created_at', 'idx_ai_rec_created_at');
                $table->index(['user_id', 'created_at'], 'idx_ai_rec_user_created');
                $table->index(['user_id', 'type'], 'idx_ai_rec_user_type');
            });
        }

        if (Schema::hasTable('quiz_attempts')) {
            Schema::table('quiz_attempts', function (Blueprint $table) {
                $table->index('user_id', 'idx_quiz_attempts_user_id');
                $table->index('quiz_id', 'idx_quiz_attempts_quiz_id');
                $table->index('score', 'idx_quiz_attempts_score');
                $table->index('passed', 'idx_quiz_attempts_passed');
                $table->index('created_at', 'idx_quiz_attempts_created_at');
                $table->index(['user_id', 'created_at'], 'idx_quiz_attempts_user_created');
                $table->index(['quiz_id', 'score'], 'idx_quiz_attempts_quiz_score');
            });
        }

        if (Schema::hasTable('quizzes')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->index('course_id', 'idx_quizzes_course_id');
                $table->index('module_id', 'idx_quizzes_module_id');
                $table->index('type', 'idx_quizzes_type');
                $table->index('status', 'idx_quizzes_status');
            });
        }

        if (Schema::hasTable('lesson_progress')) {
            Schema::table('lesson_progress', function (Blueprint $table) {
                $table->index('user_id', 'idx_lesson_prog_user_id');
                $table->index('lesson_id', 'idx_lesson_prog_lesson_id');
                $table->index('percent', 'idx_lesson_prog_percent');
                $table->index('completed_at', 'idx_lesson_prog_completed_at');
                $table->index('updated_at', 'idx_lesson_prog_updated_at');
                $table->index(['user_id', 'percent'], 'idx_lesson_prog_user_percent');
            });
        }

        if (Schema::hasTable('enrollments')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->index('student_id', 'idx_enrollments_student_id');
                $table->index('course_id', 'idx_enrollments_course_id');
                $table->index('status', 'idx_enrollments_status');
                $table->index(['student_id', 'status'], 'idx_enrollments_student_status');
            });
        }

        if (Schema::hasTable('faculties')) {
            Schema::table('faculties', function (Blueprint $table) {
                $table->index('is_active', 'idx_faculties_is_active');
            });
        }

        if (Schema::hasTable('departments')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->index('faculty_id', 'idx_departments_faculty_id');
                $table->index('is_active', 'idx_departments_is_active');
            });
        }

        if (Schema::hasTable('majors')) {
            Schema::table('majors', function (Blueprint $table) {
                $table->index('department_id', 'idx_majors_department_id');
                $table->index('is_active', 'idx_majors_is_active');
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('role', 'idx_users_role');
                $table->index('status', 'idx_users_status');
                $table->index(['role', 'status'], 'idx_users_role_status');
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->index('student_id', 'idx_payments_student_id');
                $table->index('status', 'idx_payments_status');
                $table->index('created_at', 'idx_payments_created_at');
                $table->index(['status', 'created_at'], 'idx_payments_status_created');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ai_recommendations')) {
            Schema::table('ai_recommendations', function (Blueprint $table) {
                $table->dropIndex('idx_ai_rec_user_id');
                $table->dropIndex('idx_ai_rec_type');
                $table->dropIndex('idx_ai_rec_created_at');
                $table->dropIndex('idx_ai_rec_user_created');
                $table->dropIndex('idx_ai_rec_user_type');
            });
        }

        if (Schema::hasTable('quiz_attempts')) {
            Schema::table('quiz_attempts', function (Blueprint $table) {
                $table->dropIndex('idx_quiz_attempts_user_id');
                $table->dropIndex('idx_quiz_attempts_quiz_id');
                $table->dropIndex('idx_quiz_attempts_score');
                $table->dropIndex('idx_quiz_attempts_passed');
                $table->dropIndex('idx_quiz_attempts_created_at');
                $table->dropIndex('idx_quiz_attempts_user_created');
                $table->dropIndex('idx_quiz_attempts_quiz_score');
            });
        }

        if (Schema::hasTable('quizzes')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->dropIndex('idx_quizzes_course_id');
                $table->dropIndex('idx_quizzes_module_id');
                $table->dropIndex('idx_quizzes_type');
                $table->dropIndex('idx_quizzes_status');
            });
        }

        if (Schema::hasTable('lesson_progress')) {
            Schema::table('lesson_progress', function (Blueprint $table) {
                $table->dropIndex('idx_lesson_prog_user_id');
                $table->dropIndex('idx_lesson_prog_lesson_id');
                $table->dropIndex('idx_lesson_prog_percent');
                $table->dropIndex('idx_lesson_prog_completed_at');
                $table->dropIndex('idx_lesson_prog_updated_at');
                $table->dropIndex('idx_lesson_prog_user_percent');
            });
        }

        if (Schema::hasTable('enrollments')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->dropIndex('idx_enrollments_student_id');
                $table->dropIndex('idx_enrollments_course_id');
                $table->dropIndex('idx_enrollments_status');
                $table->dropIndex('idx_enrollments_student_status');
            });
        }

        if (Schema::hasTable('faculties')) {
            Schema::table('faculties', function (Blueprint $table) {
                $table->dropIndex('idx_faculties_is_active');
            });
        }

        if (Schema::hasTable('departments')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->dropIndex('idx_departments_faculty_id');
                $table->dropIndex('idx_departments_is_active');
            });
        }

        if (Schema::hasTable('majors')) {
            Schema::table('majors', function (Blueprint $table) {
                $table->dropIndex('idx_majors_department_id');
                $table->dropIndex('idx_majors_is_active');
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('idx_users_role');
                $table->dropIndex('idx_users_status');
                $table->dropIndex('idx_users_role_status');
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropIndex('idx_payments_student_id');
                $table->dropIndex('idx_payments_status');
                $table->dropIndex('idx_payments_created_at');
                $table->dropIndex('idx_payments_status_created');
            });
        }
    }
};
