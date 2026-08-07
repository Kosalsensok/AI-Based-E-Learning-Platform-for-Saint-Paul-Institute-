<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faculties', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('name_kh')->nullable();
            $t->string('code', 50)->unique()->nullable();
            $t->string('dean')->nullable();
            $t->string('email')->nullable();
            $t->integer('est_year')->nullable();
            $t->text('description')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('departments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('faculty_id')->nullable()->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('name_kh')->nullable();
            $t->string('code', 50)->unique()->nullable();
            $t->string('head')->nullable();
            $t->string('email')->nullable();
            $t->text('description')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('majors', function (Blueprint $t) {
            $t->id();
            $t->foreignId('department_id')->nullable()->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('name_kh')->nullable();
            $t->string('code', 50)->unique()->nullable();
            $t->decimal('price_per_subject', 10, 2)->nullable();
            $t->string('duration')->nullable();
            $t->string('degree_level')->nullable();
            $t->integer('credits')->nullable();
            $t->string('language')->nullable();
            $t->text('description')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('majors');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('faculties');
    }
};
