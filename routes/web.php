<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboard;
use App\Http\Controllers\Doctor\AppointmentController as DoctorAppointment;
use App\Http\Controllers\Patient\DashboardController as PatientDashboard;
use App\Http\Controllers\Patient\AppointmentController as PatientAppointment;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;

// Doctor Controllers
use App\Http\Controllers\Doctor\LabResultController as DoctorLabResultController;
use App\Http\Controllers\Doctor\PrescriptionController as DoctorPrescriptionController;
use App\Http\Controllers\Doctor\AvailabilityController; // ✅ For doctor availability

// Patient Controllers
use App\Http\Controllers\Patient\LabResultController as PatientLabResultController;
use App\Http\Controllers\Patient\PrescriptionController as PatientPrescriptionController;

// ==================== LANDING PAGE ==================== //
Route::get('/', fn() => view('welcome'))->name('home');

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

// ==================== PROFILE ROUTES ==================== //
Route::middleware(['auth'])->group(function () {

    // Doctor profile
    Route::middleware(['role:doctor'])
        ->prefix('doctor')
        ->as('doctor.')
        ->group(function () {
            Route::get('/profile', [ProfileController::class, 'editDoctor'])->name('profile');
            Route::get('/profile/edit', [ProfileController::class, 'editDoctor'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'updateDoctor'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        });

    // Patient profile
    Route::middleware(['role:patient'])
        ->prefix('patient')
        ->as('patient.')
        ->group(function () {
            Route::get('/profile', [ProfileController::class, 'editPatient'])->name('profile');
            Route::get('/profile/edit', [ProfileController::class, 'editPatient'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'updatePatient'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        });
});

// ==================== DOCTOR ROUTES ==================== //
Route::middleware(['auth', 'role:doctor'])
    ->prefix('doctor')
    ->as('doctor.')
    ->group(function () {
        Route::get('/dashboard', [DoctorDashboard::class, 'index'])->name('dashboard');

        // ✅ Appointments
        Route::patch('/appointments/{appointment}/status', [DoctorAppointment::class, 'updateStatus'])->name('appointments.updateStatus');

        // ✅ Doctor Availability (Calendar Integration)
        Route::get('/availability', [AvailabilityController::class, 'index'])->name('availability.index');
        Route::get('/availability/events', [AvailabilityController::class, 'getEvents'])->name('availability.events'); // <-- Added for calendar JSON
        Route::post('/availability', [AvailabilityController::class, 'store'])->name('availability.store');
        Route::delete('/availability/{availability}', [AvailabilityController::class, 'destroy'])->name('availability.destroy');
        
        // Lab Results
        Route::get('/lab-results', [DoctorLabResultController::class, 'index'])->name('lab_results.index');
        Route::post('/lab-results', [DoctorLabResultController::class, 'store'])->name('lab_results.store');

        // Prescriptions
        Route::get('/prescriptions', [DoctorPrescriptionController::class, 'index'])->name('prescriptions.index');
        Route::post('/prescriptions', [DoctorPrescriptionController::class, 'store'])->name('prescriptions.store');
    });

// ==================== PATIENT ROUTES ==================== //
Route::middleware(['auth', 'role:patient'])
    ->prefix('patient')
    ->as('patient.')
    ->group(function () {
        Route::get('/dashboard', [PatientDashboard::class, 'index'])->name('dashboard');

        // Doctor profile view
        Route::get('/doctor/{id}', [PatientDashboard::class, 'showDoctor'])->name('showDoctor');

        // ✅ Appointments (with Gmail)
        Route::post('/appointments', [PatientAppointment::class, 'store'])->name('appointments.store');
        Route::patch('/appointments/{appointment}/cancel', [PatientAppointment::class, 'cancel'])->name('appointments.cancel');
        Route::patch('/appointments/{appointment}/reschedule', [PatientAppointment::class, 'reschedule'])->name('appointments.reschedule');

        // Lab Results & Prescriptions
        Route::get('/lab-results', [PatientLabResultController::class, 'index'])->name('labResults.index');
        Route::get('/prescriptions', [PatientPrescriptionController::class, 'index'])->name('prescriptions.index');
    });
