<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\FollowUpSubmission;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin
        Admin::updateOrCreate(
            ['email' => 'admin@hospital.ke'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Clinician
        Doctor::updateOrCreate(
            ['email' => 'clinician@hospital.ke'],
            [
                'name' => 'Dr. Demo Clinician',
                'password' => Hash::make('password'),
                // 'specialization' => 'General Practice', // if exists
            ]
        );

        // 3. Patient
        $patient = Patient::updateOrCreate(
            ['email' => 'patient@test.ke'],
            [
                'name' => 'Ifay Test',
                'password' => Hash::make('password'),
            ]
        );

        // 4. Create Demo Submission (if the columns match)
        // Assuming FollowUpSubmission has patient_id, symptoms, urgency, status
        FollowUpSubmission::firstOrCreate(
            ['patient_id' => $patient->id],
            [
                'symptoms' => json_encode(['Fever', 'Pain', 'Swelling']),
                'urgency' => 'High',
                'status' => 'Pending Review',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ]
        );
        
        $this->command->info('Demo data seeded successfully!');
    }
}
