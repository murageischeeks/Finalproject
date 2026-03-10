<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emr_observations', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('person')->nullable();
            $table->string('concept')->nullable();
            $table->timestamp('obs_datetime')->nullable();
            $table->text('value');
            $table->string('comment')->nullable();
            $table->foreignId('follow_up_submission_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emr_observations');
    }
};