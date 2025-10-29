<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrescriptionsTable extends Migration
{
    public function up()
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('patient_id');
            $table->text('medicines'); // free-text list: "Paracetamol 500mg - 1 tab 8hr x5d"
            $table->text('notes')->nullable(); // extra instructions
            $table->string('file_path')->nullable(); // optional scanned prescription
            $table->enum('status', ['pending','collected'])->default('pending');
            $table->timestamps();

            $table->index('doctor_id');
            $table->index('patient_id');

            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('prescriptions');
    }
}
