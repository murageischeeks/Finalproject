<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_up_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->json('symptom_categories');
            $table->unsignedTinyInteger('severity'); // 1–5
            $table->enum('recovery_status', ['Improving', 'Stable', 'Worsening', 'Uncertain']);
            $table->text('notes')->nullable();
            $table->enum('urgency_level', ['High', 'Medium', 'Low'])->nullable(); // set by triage engine
            $table->enum('sync_status', ['Pending', 'Synced', 'Failed'])->default('Pending');
            $table->string('openmrs_observation_uuid')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('doctor_response')->nullable();
            $table->timestamps();

            // Indexes for dashboard sorting performance
            $table->index('patient_id');
            $table->index('urgency_level');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_up_submissions');
    }
};