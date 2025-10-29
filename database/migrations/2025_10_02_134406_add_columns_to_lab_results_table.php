<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lab_results', function (Blueprint $table) {
            // Add doctor_id column if it doesn't exist
            if (!Schema::hasColumn('lab_results', 'doctor_id')) {
                $table->unsignedBigInteger('doctor_id')->after('id')->nullable();
                $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            }

            // Add test_type column if it doesn't exist
            if (!Schema::hasColumn('lab_results', 'test_type')) {
                $table->string('test_type')->after('patient_id')->nullable(); // nullable to avoid NOT NULL issues
            }

            // If you want, you can also ensure notes & file_path exist
            if (!Schema::hasColumn('lab_results', 'notes')) {
                $table->text('notes')->nullable()->after('test_type');
            }
            if (!Schema::hasColumn('lab_results', 'file_path')) {
                $table->string('file_path')->after('notes')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('lab_results', function (Blueprint $table) {
            if (Schema::hasColumn('lab_results', 'doctor_id')) {
                $table->dropForeign(['doctor_id']);
                $table->dropColumn('doctor_id');
            }
            if (Schema::hasColumn('lab_results', 'test_type')) {
                $table->dropColumn('test_type');
            }
            if (Schema::hasColumn('lab_results', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('lab_results', 'file_path')) {
                $table->dropColumn('file_path');
            }
        });
    }
};
