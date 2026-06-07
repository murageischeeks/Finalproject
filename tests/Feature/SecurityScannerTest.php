<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\FollowUpSubmission;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityScannerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        \Illuminate\Support\Facades\Schema::connection('emr')->dropIfExists('emr_observations');
        \Illuminate\Support\Facades\Schema::connection('emr')->create('emr_observations', function ($table) {
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

    public function test_clean_submission_passes_security_scanner()
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $doctor = User::factory()->create(['role' => 'doctor']);

        $response = $this->actingAs($patient)
            ->post(route('patient.followup.store'), [
                'doctor_id' => $doctor->id,
                'symptom_categories' => ['fever', 'pain'],
                'severity' => 3,
                'recovery_status' => 'Stable',
                'notes' => 'I feel okay, just a little tired. No issues.',
            ]);

        // Should redirect to confirmation or history
        $response->assertRedirect();
        
        $submission = FollowUpSubmission::where('patient_id', $patient->id)->first();
        $this->assertNotNull($submission);
        $this->assertNotEquals('Failed', $submission->sync_status);
        $this->assertEquals('Low', $submission->urgency_level);
    }

    public function test_sqli_payload_is_blocked_and_logged()
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $doctor = User::factory()->create(['role' => 'doctor']);

        $maliciousNotes = "I feel bad. UNION SELECT username, password FROM users --";

        $response = $this->actingAs($patient)
            ->from(route('patient.followup.create'))
            ->post(route('patient.followup.store'), [
                'doctor_id' => $doctor->id,
                'symptom_categories' => ['fever'],
                'severity' => 2,
                'recovery_status' => 'Worsening',
                'notes' => $maliciousNotes,
            ]);

        // Should redirect back to create page with session error and failed submission ID
        $response->assertRedirect(route('patient.followup.create'));
        $response->assertSessionHasErrors(['security_block']);
        $response->assertSessionHas('failed_submission_id');

        $failedSubmissionId = session('failed_submission_id');
        $this->assertNotNull($failedSubmissionId);

        // Verify FollowUpSubmission record was created in 'Failed' status and 'High' urgency
        $submission = FollowUpSubmission::find($failedSubmissionId);
        $this->assertNotNull($submission);
        $this->assertEquals('Failed', $submission->sync_status);
        $this->assertEquals('High', $submission->urgency_level);
        $this->assertEquals($maliciousNotes, $submission->notes);

        // Verify Audit Log entry was created
        $auditLog = AuditLog::where('resource_id', $failedSubmissionId)
            ->where('action', 'security_checkpoint_failed')
            ->first();

        $this->assertNotNull($auditLog);
        $this->assertEquals('failure', $auditLog->outcome);
        $this->assertEquals('SQL Injection', $auditLog->meta['threat_type']);
        $this->assertContains($maliciousNotes, $auditLog->meta['payloads']);
    }

    public function test_xss_payload_is_blocked_and_logged()
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $doctor = User::factory()->create(['role' => 'doctor']);

        $maliciousNotes = "<script>alert('hack');</script>";

        $response = $this->actingAs($patient)
            ->from(route('patient.followup.create'))
            ->post(route('patient.followup.store'), [
                'doctor_id' => $doctor->id,
                'symptom_categories' => ['other'],
                'severity' => 1,
                'recovery_status' => 'Uncertain',
                'notes' => $maliciousNotes,
            ]);

        $response->assertRedirect(route('patient.followup.create'));
        $response->assertSessionHasErrors(['security_block']);
        $response->assertSessionHas('failed_submission_id');

        $failedSubmissionId = session('failed_submission_id');

        // Verify FollowUpSubmission record was created in 'Failed' status and 'High' urgency
        $submission = FollowUpSubmission::find($failedSubmissionId);
        $this->assertNotNull($submission);
        $this->assertEquals('Failed', $submission->sync_status);
        $this->assertEquals('High', $submission->urgency_level);

        // Verify Audit Log entry was created
        $auditLog = AuditLog::where('resource_id', $failedSubmissionId)
            ->where('action', 'security_checkpoint_failed')
            ->first();

        $this->assertNotNull($auditLog);
        $this->assertEquals('failure', $auditLog->outcome);
        $this->assertEquals('Cross-Site Scripting (XSS)', $auditLog->meta['threat_type']);
        $this->assertContains($maliciousNotes, $auditLog->meta['payloads']);
    }

    public function test_mixed_sqli_and_xss_payload_is_blocked_and_logged()
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $doctor = User::factory()->create(['role' => 'doctor']);

        $maliciousNotes = "when are going to the club today alert('which pub') insert into (patientid,patientname) values('123','stanley')";

        $response = $this->actingAs($patient)
            ->from(route('patient.followup.create'))
            ->post(route('patient.followup.store'), [
                'doctor_id' => $doctor->id,
                'symptom_categories' => ['fever'],
                'severity' => 2,
                'recovery_status' => 'Worsening',
                'notes' => $maliciousNotes,
            ]);

        $response->assertRedirect(route('patient.followup.create'));
        $response->assertSessionHasErrors(['security_block']);
        $response->assertSessionHas('failed_submission_id');

        $failedSubmissionId = session('failed_submission_id');

        // Verify FollowUpSubmission record was created in 'Failed' status and 'High' urgency
        $submission = FollowUpSubmission::find($failedSubmissionId);
        $this->assertNotNull($submission);
        $this->assertEquals('Failed', $submission->sync_status);
        $this->assertEquals('High', $submission->urgency_level);

        // Verify Audit Log entry was created
        $auditLog = AuditLog::where('resource_id', $failedSubmissionId)
            ->where('action', 'security_checkpoint_failed')
            ->first();

        $this->assertNotNull($auditLog);
        $this->assertEquals('SQL Injection & Cross-Site Scripting (XSS)', $auditLog->meta['threat_type']);
    }

    public function test_middleware_trace_authorization()
    {
        $patient1 = User::factory()->create(['role' => 'patient']);
        $patient2 = User::factory()->create(['role' => 'patient']);
        $doctor = User::factory()->create(['role' => 'doctor']);

        // Create a normal submission for patient1
        $submission = FollowUpSubmission::create([
            'patient_id' => $patient1->id,
            'doctor_id' => $doctor->id,
            'symptom_categories' => ['fever'],
            'severity' => 2,
            'recovery_status' => 'Stable',
            'notes' => 'Some normal notes',
            'sync_status' => 'Synced',
            'urgency_level' => 'Low',
        ]);

        // Patient 1 should be authorized to view their own trace
        $response = $this->actingAs($patient1)
            ->get(route('middleware.trace', $submission->id));
        $response->assertStatus(200);

        // Patient 2 should not be authorized to view Patient 1's trace (returns 403)
        $response = $this->actingAs($patient2)
            ->get(route('middleware.trace', $submission->id));
        $response->assertStatus(403);

        // Doctor should be authorized to view Patient 1's trace (since they are the assigned doctor)
        $response = $this->actingAs($doctor, 'doctor')
            ->get(route('middleware.trace', $submission->id));
        $response->assertStatus(200);
    }

    public function test_manual_sync_is_denied_for_security_failed_submissions()
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $doctor = User::factory()->create(['role' => 'doctor']);

        $submission = FollowUpSubmission::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'symptom_categories' => ['fever'],
            'severity' => 2,
            'recovery_status' => 'Stable',
            'notes' => 'SELECT * FROM users',
            'sync_status' => 'Failed',
            'urgency_level' => 'High',
        ]);

        // Create the security failed audit log
        \App\Services\AuditLogService::log(
            action: 'security_checkpoint_failed',
            resourceType: 'follow_up_submission',
            resourceId: $submission->id,
            outcome: 'failure',
            meta: [
                'threat_type' => 'SQL Injection',
                'patterns' => ['SELECT'],
                'payloads' => ['SELECT * FROM users'],
            ]
        );

        \Livewire\Livewire::actingAs($doctor, 'doctor')
            ->test(\App\Livewire\MiddlewareTrace::class, ['submissionId' => $submission->id])
            ->call('scheduleManualSync');
            
        // Should remain 'Failed'
        $this->assertEquals('Failed', $submission->fresh()->sync_status);
    }

    public function test_manual_sync_dispatches_job_for_normal_failures()
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $doctor = User::factory()->create(['role' => 'doctor']);

        $submission = FollowUpSubmission::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'symptom_categories' => ['fever'],
            'severity' => 2,
            'recovery_status' => 'Stable',
            'notes' => 'Regular symptoms',
            'sync_status' => 'Failed',
            'urgency_level' => 'Low',
        ]);

        \Illuminate\Support\Facades\Bus::fake();

        \Livewire\Livewire::actingAs($doctor, 'doctor')
            ->test(\App\Livewire\MiddlewareTrace::class, ['submissionId' => $submission->id])
            ->call('scheduleManualSync');

        // Should change to 'Pending'
        $this->assertEquals('Pending', $submission->fresh()->sync_status);
        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\ProcessFollowUpSubmission::class);
    }

    public function test_contact_it_support_creates_ticket_and_logs_audit()
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $doctor = User::factory()->create(['role' => 'doctor']);

        $submission = FollowUpSubmission::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'symptom_categories' => ['fever'],
            'severity' => 2,
            'recovery_status' => 'Stable',
            'notes' => 'Regular symptoms',
            'sync_status' => 'Failed',
            'urgency_level' => 'Low',
        ]);

        \Livewire\Livewire::actingAs($doctor, 'doctor')
            ->test(\App\Livewire\MiddlewareTrace::class, ['submissionId' => $submission->id])
            ->call('contactItSupport');

        $this->assertTrue(AuditLog::where('action', 'it_support_ticket_opened')
            ->where('resource_id', $submission->id)
            ->exists());
    }
}
