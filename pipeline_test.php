<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $sub = App\Models\FollowUpSubmission::create([
        'patient_id' => 4,
        'doctor_id' => 3,
        'symptom_categories' => ['pain', 'fever'],
        'severity' => 4,
        'recovery_status' => 'Uncertain',
        'notes' => 'Automated test pipeline execution',
        'urgency_level' => 'High',
        'sync_status' => 'Pending'
    ]);

    echo "1. Created submission #" . $sub->id . " (Status: Pending)\n";

    App\Jobs\ProcessFollowUpSubmission::dispatchSync($sub);

    $sub->refresh();

    if ($sub->sync_status === 'Synced' && !empty($sub->openmrs_observation_uuid)) {
        echo "2. SUCCESS! Submission synced to EMR.\n";
        echo "   UUID: " . $sub->openmrs_observation_uuid . "\n";
    } else {
        echo "2. FAILED! Sync status is: " . $sub->sync_status . "\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
