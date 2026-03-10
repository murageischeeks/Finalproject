<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('triage_rules', function (Blueprint $table) {
            $table->id();
            // rule_type: 'symptom' | 'severity_multiplier' | 'recovery_modifier'
            $table->string('rule_type');
            $table->string('key');       // e.g. 'fever', '5', 'Worsening'
            $table->decimal('weight', 5, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('triage_rules');
    }
};