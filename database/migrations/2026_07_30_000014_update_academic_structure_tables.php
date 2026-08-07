<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faculties', function (Blueprint $table) {
            if (!Schema::hasColumn('faculties', 'dean')) {
                $table->string('dean')->nullable()->after('code');
            }
            if (!Schema::hasColumn('faculties', 'email')) {
                $table->string('email')->nullable()->after('dean');
            }
            if (!Schema::hasColumn('faculties', 'est_year')) {
                $table->integer('est_year')->nullable()->after('email');
            }
        });

        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'head')) {
                $table->string('head')->nullable()->after('code');
            }
            if (!Schema::hasColumn('departments', 'email')) {
                $table->string('email')->nullable()->after('head');
            }
        });

        Schema::table('majors', function (Blueprint $table) {
            if (!Schema::hasColumn('majors', 'price_per_subject')) {
                $table->decimal('price_per_subject', 10, 2)->nullable()->after('code');
            }
            if (!Schema::hasColumn('majors', 'duration')) {
                $table->string('duration')->nullable()->after('price_per_subject');
            }
            if (!Schema::hasColumn('majors', 'degree_level')) {
                $table->string('degree_level')->nullable()->after('duration');
            }
            if (!Schema::hasColumn('majors', 'credits')) {
                $table->integer('credits')->nullable()->after('degree_level');
            }
            if (!Schema::hasColumn('majors', 'language')) {
                $table->string('language')->nullable()->after('credits');
            }
        });
    }

    public function down(): void
    {
    }
};
