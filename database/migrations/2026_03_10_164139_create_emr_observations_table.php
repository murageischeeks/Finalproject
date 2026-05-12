<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('emr')->create('emr_observations', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('person')->nullable();
            $table->string('concept')->nullable();
            $table->timestamp('obs_datetime')->nullable();
            $table->text('value');
            $table->string('comment')->nullable();
            $table->unsignedBigInteger('follow_up_submission_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('emr')->dropIfExists('emr_observations');
    }
};