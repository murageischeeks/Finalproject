<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('prescriptions', function (Blueprint $table) {
        $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('prescriptions', function (Blueprint $table) {
        $table->dropConstrainedForeignId('doctor_id');
    });
}

};
