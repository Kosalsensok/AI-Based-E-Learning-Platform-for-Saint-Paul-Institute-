<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('semesters_count')->default(2);
            $table->string('status')->default('active'); // active, completed, upcoming
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('parent_year')->nullable();
            $table->string('semester_num')->default('Semester 1');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('enrollment_open')->nullable();
            $table->date('enrollment_close')->nullable();
            $table->string('midterm_exam')->nullable();
            $table->string('final_exam')->nullable();
            $table->date('payment_due')->nullable();
            $table->string('late_fee')->default('$5 per week overdue');
            $table->string('status')->default('active'); // active, completed, planned
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('academic_years');
    }
};
