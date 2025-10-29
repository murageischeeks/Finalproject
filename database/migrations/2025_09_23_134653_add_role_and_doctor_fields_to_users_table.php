<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // role: patient, doctor, admin
            $table->enum('role', ['patient', 'doctor', 'admin'])
                ->default('patient')
                ->after('email');

            // doctor's fields
            $table->string('license_number')->nullable()->after('role');
            $table->string('department')->nullable()->after('license_number');
            $table->string('specialization')->nullable()->after('department');

            // whether admin has verified doctor's license
            $table->boolean('license_verified')->default(false)->after('specialization');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'license_number',
                'department',
                'specialization',
                'license_verified',
            ]);
        });
    }
};
