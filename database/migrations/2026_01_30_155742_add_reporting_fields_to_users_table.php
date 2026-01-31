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
            $table->string('employee_code', 50)->nullable()->after('email');
            $table->string('designation', 100)->nullable()->after('employee_code');
            $table->string('department', 100)->nullable()->after('designation');
            $table->foreignId('reports_to')->nullable()->after('department')
                ->constrained('users')->nullOnDelete();
            $table->string('profile_photo', 500)->nullable()->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['reports_to']);
            $table->dropColumn([
                'employee_code',
                'designation',
                'department',
                'reports_to',
                'profile_photo',
            ]);
        });
    }
};
