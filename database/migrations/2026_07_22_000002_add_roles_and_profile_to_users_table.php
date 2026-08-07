<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->enum('role', ['admin', 'teacher', 'student'])->default('student')->after('password');
            $t->foreignId('major_id')->nullable()->constrained('majors')->nullOnDelete()->after('role');
            $t->string('phone')->nullable()->after('major_id');
            $t->string('avatar')->nullable()->after('phone');
            $t->boolean('is_active')->default(true)->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->dropForeign(['major_id']);
            $t->dropColumn(['role', 'major_id', 'phone', 'avatar', 'is_active']);
        });
    }
};
