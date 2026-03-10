<?php

namespace App\Services\Middleware;

use App\Models\FollowUpSubmission;

class SubmissionTransformer
{
    // ── SNOMED CT Clinical Codes ───────────────────────────────
    // Standard clinical terminology for symptom mapping
    private array $snomedCodes = [
        'fever'                  => ['code' => '386661006', 'display' => 'Fever (finding)'],
        'pain'                   => ['code' => '22253000',  'display' => 'Pain (finding)'],
        'swelling'               => ['code' => '65124004',  'display' => 'Swelling (finding)'],
        'medication_side_effect' => ['code' => '281647001', 'display' => 'Adverse reaction to drug (disorder)'],
        'wound_concern'          => ['code' => '416940007', 'display' => 'Past history of wound care (situation)'],
        'general_deterioration'  => ['code' => '271299001', 'display' => 'Patient condition deteriorated (finding)'],
    ];

    // ── LOINC Codes for recovery status ───────────────────────
    private array $loincStatus = [
        'Improving' => ['code' => 'LA25750-7', 'display' => 'Improving'],
        'Stable'    => ['code' => 'LA25751-5', 'display' => 'Stable'],
        'Worsening' => ['code' => 'LA25752-3', 'display' => 'Worsening'],
        'Uncertain' => ['code' => 'LA25753-1', 'display' => 'Uncertain'],
    ];

    // ── OpenMRS Concept UUIDs (sandbox concept dictionary) ────
    private array $openMRSConcepts = [
        'follow_up_observation' => 'c8a8a7a0-1234-4567-89ab-follow-up-obs',
        'severity'              => 'a09ab2c5-088b-44ce-9260-f7b8b4b3d11d',
        'recovery_status'       => '163006AAAAAAAAAAAAAAAAAAAAAAAAAAAAAA',
        'symptoms'              => '1284AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA',
    ];

    public function toOpenMRSObservation(FollowUpSubmission $submission): array
    {
        return [
            // ── FHIR R4 Observation Resource Format ───────────
            'resourceType'    => 'Observation',
            'status'          => 'final',

            // Patient reference using OpenMRS UUID
            'person'          => $submission->patient->openmrs_uuid ?? 'unknown',

            // Concept UUID for follow-up observation type
            'concept'         => $this->openMRSConcepts['follow_up_observation'],

            // ISO 8601 timestamp
            'obsDatetime'     => $submission->created_at->toIso8601String(),

            'comment'         => 'Synced from BleakHospital Patient Follow-Up System v1.0',

            // ── Structured Clinical Value ──────────────────────
            'value'           => $this->buildClinicalValue($submission),

            // ── Component Observations (FHIR style) ───────────
            'component'       => $this->buildComponents($submission),
        ];
    }

    private function buildClinicalValue(FollowUpSubmission $submission): array
    {
        return [
            // Symptoms mapped to SNOMED CT codes
            'symptoms' => array_map(function ($symptom) {
                $snomed = $this->snomedCodes[$symptom] ?? ['code' => 'unknown', 'display' => $symptom];
                return [
                    'system'  => 'http://snomed.info/sct',
                    'code'    => $snomed['code'],
                    'display' => $snomed['display'],
                    'raw'     => $symptom,
                ];
            }, $submission->symptom_categories),

            // Severity as a numeric score (0-5 scale, FHIR quantity)
            'severity' => [
                'value'  => $submission->severity,
                'unit'   => 'score',
                'system' => 'http://unitsofmeasure.org',
                'code'   => '{score}',
            ],

            // Recovery status mapped to LOINC
            'recoveryStatus' => [
                'system'  => 'http://loinc.org',
                'code'    => $this->loincStatus[$submission->recovery_status]['code'] ?? 'unknown',
                'display' => $this->loincStatus[$submission->recovery_status]['display'] ?? $submission->recovery_status,
            ],

            // Urgency classification
            'urgencyLevel' => $submission->urgency_level,

            // Free text notes
            'patientNotes' => $submission->notes,

            // Submission metadata
            'submittedAt'  => $submission->created_at->toIso8601String(),
            'facilityNote' => 'Mbagathi County Referral Hospital — General Outpatient Department',
        ];
    }

    private function buildComponents(FollowUpSubmission $submission): array
    {
        return [
            [
                'code'         => ['coding' => [['system' => 'http://loinc.org', 'code' => '75325-1', 'display' => 'Symptom']]],
                'valueString'  => implode(', ', array_map(
                    fn($s) => $this->snomedCodes[$s]['display'] ?? $s,
                    $submission->symptom_categories
                )),
            ],
            [
                'code'         => ['coding' => [['system' => 'http://loinc.org', 'code' => '72514-3', 'display' => 'Pain severity']]],
                'valueInteger' => $submission->severity,
            ],
            [
                'code'        => ['coding' => [['system' => 'http://loinc.org', 'code' => '11332-4', 'display' => 'History of illness']]],
                'valueString' => $submission->recovery_status,
            ],
        ];
    }
}