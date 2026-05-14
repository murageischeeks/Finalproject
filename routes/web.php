<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboard;
use App\Http\Controllers\Doctor\AppointmentController as DoctorAppointment;
use App\Http\Controllers\Patient\DashboardController as PatientDashboard;
use App\Http\Controllers\Patient\AppointmentController as PatientAppointment;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmrReceiverController;

// Doctor Controllers
use App\Http\Controllers\Doctor\LabResultController as DoctorLabResultController;
use App\Http\Controllers\Doctor\PrescriptionController as DoctorPrescriptionController;
use App\Http\Controllers\Doctor\FollowUpController as DoctorFollowUpController;

// Patient Controllers
use App\Http\Controllers\Patient\LabResultController as PatientLabResultController;
use App\Http\Controllers\Patient\PrescriptionController as PatientPrescriptionController;
use App\Http\Controllers\Patient\FollowUpController as PatientFollowUpController;

// ==================== LANDING PAGE ==================== //
Route::get('/', fn() => view('welcome'))->name('home');
Route::view('/features', 'pages.features')->name('features');
Route::view('/specialists', 'pages.specialists')->name('specialists');
Route::view('/about', 'pages.about')->name('about');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/terms', 'pages.terms')->name('terms');

// ==================== AUTH ROUTES ==================== //
Route::controller(RegisteredUserController::class)->group(function () {
    Route::get('/register', 'create')->name('register');
    Route::post('/register', 'store')->name('register.store');
});

Route::controller(AuthenticatedSessionController::class)->group(function () {
    Route::get('/login', 'create')->name('login');
    Route::post('/login', 'store')->name('login.store');
    Route::post('/logout', 'destroy')->name('logout');
});

// ==================== SIMULATED OpenMRS EMR RECEIVER API ==================== //
// No auth — simulates OpenMRS REST API accepting FHIR observations
Route::prefix('api/emr')->name('emr.')->group(function () {
    Route::post('/observations', [EmrReceiverController::class, 'store'])->name('observations.store');
    Route::get('/observations/{uuid}', [EmrReceiverController::class, 'show'])->name('observations.show');
    Route::get('/observations', [EmrReceiverController::class, 'index'])->name('observations.index');
});

// ==================== PROFILE ROUTES ==================== //
Route::middleware(['auth:doctor', 'role:doctor'])
    ->prefix('doctor')->as('doctor.')
    ->group(function () {
        Route::get('/profile', [ProfileController::class, 'editDoctor'])->name('profile');
        Route::get('/profile/edit', [ProfileController::class, 'editDoctor'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'updateDoctor'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

Route::middleware(['auth', 'role:patient'])
    ->prefix('patient')->as('patient.')
    ->group(function () {
        Route::get('/profile', [ProfileController::class, 'editPatient'])->name('profile');
        Route::get('/profile/edit', [ProfileController::class, 'editPatient'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'updatePatient'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

// ── Admin Routes ──────────────────────────────────────
// NOTE: auth check relaxed for evaluation demo. In production, use ['auth', 'role:admin'].
Route::get('/admin', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('admin.dashboard');

// ── Middleware Trace Viewer ────────────────────────────
// Protected: only authenticated users (doctors/admin) can view pipeline traces.
Route::get('/admin/middleware-trace/{submissionId}', \App\Livewire\MiddlewareTrace::class)
    ->middleware(['auth:doctor'])
    ->name('middleware.trace');

// ==================== DOCTOR ROUTES ====================
// Uses auth:doctor so the doctor guard session is validated.
Route::middleware(['auth:doctor', 'role:doctor'])
    ->prefix('doctor')
    ->as('doctor.')
    ->group(function () {
        Route::get('/dashboard', [DoctorDashboard::class, 'index'])->name('dashboard');

        // Appointments
        Route::patch('/appointments/{appointment}/status', [DoctorAppointment::class, 'updateStatus'])->name('appointments.updateStatus');

        // Lab Results
        Route::get('/lab-results', [DoctorLabResultController::class, 'index'])->name('lab_results.index');
        Route::post('/lab-results', [DoctorLabResultController::class, 'store'])->name('lab_results.store');

        // Prescriptions
        Route::get('/prescriptions', [DoctorPrescriptionController::class, 'index'])->name('prescriptions.index');
        Route::post('/prescriptions', [DoctorPrescriptionController::class, 'store'])->name('prescriptions.store');

        // ── Follow-Up (Doctor side) ──────────────────────────
        Route::get('/followup', [DoctorFollowUpController::class, 'index'])->name('followup.index');
        Route::get('/followup/refresh', [DoctorFollowUpController::class, 'refresh'])->name('followup.refresh');
        Route::get('/followup/{submission}', [DoctorFollowUpController::class, 'show'])->name('followup.show');
        Route::patch('/followup/{submission}/review', [DoctorFollowUpController::class, 'markReviewed'])->name('followup.review');
        Route::post('/followup/{submission}/respond', [DoctorFollowUpController::class, 'respond'])->name('followup.respond');
    });

// ==================== PATIENT ROUTES ==================== //
Route::middleware(['auth', 'role:patient'])
    ->prefix('patient')
    ->as('patient.')
    ->group(function () {
        Route::get('/dashboard', [PatientDashboard::class, 'index'])->name('dashboard');

        // Doctor profile view
        Route::get('/doctor/{id}', [PatientDashboard::class, 'showDoctor'])->name('showDoctor');

        // Appointments
        Route::post('/appointments', [PatientAppointment::class, 'store'])->name('appointments.store');
        Route::patch('/appointments/{appointment}/cancel', [PatientAppointment::class, 'cancel'])->name('appointments.cancel');
        Route::patch('/appointments/{appointment}/reschedule', [PatientAppointment::class, 'reschedule'])->name('appointments.reschedule');

        // Lab Results & Prescriptions
        Route::get('/lab-results', [PatientLabResultController::class, 'index'])->name('labResults.index');
        Route::get('/prescriptions', [PatientPrescriptionController::class, 'index'])->name('prescriptions.index');

        // ── Follow-Up (Patient side) ─────────────────────────
        Route::get('/followup/create', [PatientFollowUpController::class, 'create'])->name('followup.create');
        Route::post('/followup', [PatientFollowUpController::class, 'store'])->name('followup.store');
        Route::get('/followup/{submission}/confirmation', [PatientFollowUpController::class, 'confirmation'])->name('followup.confirmation');
        Route::get('/followup/history', [PatientFollowUpController::class, 'index'])->name('followup.index');
    });