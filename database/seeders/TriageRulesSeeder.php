<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TriageRulesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('triage_rules')->truncate();

        // ── Symptom Weights ────────────────────────────────────
        // Raised so a single serious symptom can reach Medium/High
        $symptoms = [
            'fever'                  => 30,
            'pain'                   => 25,
            'swelling'               => 20,
            'medication_side_effect' => 35,
            'wound_concern'          => 32,
            'general_deterioration'  => 40,
            'other'                  => 20,
        ];

        foreach ($symptoms as $key => $weight) {
            DB::table('triage_rules')->insert([
                'rule_type'  => 'symptom_weight',
                'key'        => $key,
                'weight'     => $weight,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ── Severity Multipliers ───────────────────────────────
        $multipliers = [
            '1' => 0.4,
            '2' => 0.7,
            '3' => 1.0,
            '4' => 1.4,
            '5' => 1.8,
        ];

        foreach ($multipliers as $key => $weight) {
            DB::table('triage_rules')->insert([
                'rule_type'  => 'severity_multiplier',
                'key'        => $key,
                'weight'     => $weight,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ── Recovery Modifiers ─────────────────────────────────
        $modifiers = [
            'Improving' => -15,
            'Stable'    =>   0,
            'Uncertain' =>  15,
            'Worsening' =>  30,
        ];

        foreach ($modifiers as $key => $weight) {
            DB::table('triage_rules')->insert([
                'rule_type'  => 'recovery_modifier',
                'key'        => $key,
                'weight'     => $weight,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}