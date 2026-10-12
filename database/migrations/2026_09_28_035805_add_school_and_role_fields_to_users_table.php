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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->enum('role', ['student', 'teacher', 'staff', 'stand_owner', 'admin'])->default('student')->after('phone');
            $table->string('class_or_division', 60)->nullable()->after('role');
            $table->string('nisn_or_nip', 30)->nullable()->after('class_or_division');
            $table->string('avatar')->nullable()->after('nisn_or_nip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role', 'class_or_division', 'nisn_or_nip', 'avatar']);
        });
    }
};
