<?php

namespace App\Http\Controllers;

use App\Models\EmrObservation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmrReceiverController extends Controller
{
    // ── Simulated OpenMRS REST API endpoint ───────────────────
    // Receives FHIR-formatted observation payload from middleware
    // and stores it as if it were OpenMRS accepting the observation

    public function store(Request $request)
    {
        // ── Step 1: Validate incoming FHIR payload ─────────────
        $validated = $request->validate([
            'resourceType' => 'required|string',
            'person'       => 'nullable|string',
            'concept'      => 'required|string',
            'obsDatetime'  => 'required|string',
            'value'        => 'required|array',
            'comment'      => 'nullable|string',
        ]);

        // ── Step 2: Generate a UUID (simulating OpenMRS response)
        $observationUuid = (string) Str::uuid();

        // ── Step 3: Store the observation in emr_observations ──
        EmrObservation::create([
            'uuid'                    => $observationUuid,
            'person'                  => $validated['person'] ?? 'unknown',
            'concept'                 => $validated['concept'],
            'obs_datetime'            => now()->parse($validated['obsDatetime']),
            'value'                   => json_encode($validated['value']),
            'comment'                 => $validated['comment'] ?? null,
            'follow_up_submission_id' => $request->input('follow_up_submission_id'),
        ]);

        // ── Step 4: Return OpenMRS-style 201 Created response ──
        return response()->json([
            'uuid'        => $observationUuid,
            'resourceType'=> 'Observation',
            'status'      => 'final',
            'display'     => 'Follow-Up Observation — ' . $observationUuid,
            'links'       => [
                [
                    'rel'  => 'self',
                    'uri'  => url('/api/emr/observations/' . $observationUuid),
                ]
            ],
        ], 201);
    }

    // ── View a stored observation by UUID ─────────────────────
    public function show(string $uuid)
    {
        $observation = EmrObservation::where('uuid', $uuid)->firstOrFail();

        $valueData = json_decode($observation->value, true) ?? [];
        $patientName = $valueData['patientName'] ?? 'Unknown Patient';
        
        return response()->json([
            'uuid'         => $observation->uuid,
            'person'       => $observation->person,
            'patientName'  => $patientName,
            'concept'      => $observation->concept,
            'display'      => 'Follow-Up Observation — ' . $patientName,
            'obsDatetime'  => $observation->obs_datetime,
            'value'        => $valueData,
            'comment'      => $observation->comment,
            'links'        => [
                [
                    'rel' => 'self',
                    'uri' => url('/api/emr/observations/' . $observation->uuid),
                ]
            ],
        ]);
    }

    // ── List all observations (simulated OpenMRS patient query) 
    public function index(Request $request)
    {
        $observations = EmrObservation::when(
            $request->filled('person'),
            fn($q) => $q->where('person', $request->person)
        )->latest()->get();

        return response()->json([
            'results' => $observations->map(fn($obs) => [
                'uuid'        => $obs->uuid,
                'person'      => $obs->person,
                'obsDatetime' => $obs->obs_datetime,
                'display'     => 'Follow-Up Observation — ' . $obs->uuid,
            ]),
            'totalCount' => $observations->count(),
        ]);
    }
}