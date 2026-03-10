<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');           // 'submission_created', 'emr_synced', etc.
            $table->string('resource_type')->nullable();
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->string('outcome');          // 'success' | 'failure'
            $table->json('meta')->nullable();   // extra context, error messages
            $table->timestamp('created_at');    // no updated_at — write-only by design
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};