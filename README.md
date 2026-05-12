# Interoperable Patient Follow-Up System
> Middleware-Based EMR Integration Pipeline

![PHP Version](https://img.shields.io/badge/PHP-8.1+-777BB4.svg)
![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1.svg)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC.svg)

## System Overview

This system enables patients to submit post-consultation follow-ups via mobile or web forms. A robust middleware layer handles validation, transformation, and synchronization to hospital EMR systems (such as OpenMRS or KenyaEMR).

Core Innovation: A middleware pipeline providing complete observability into data processing, validation, and EMR integration.

---

## Prerequisites

Before you begin, ensure you have met the following requirements:
- PHP 8.1 or higher
- Composer 2.x
- MySQL 8.0 or higher
- Node.js 16+ & NPM
- Git

---

## Installation Steps

1. Clone the repository
   ```bash
   git clone https://github.com/murageischeeks/Finalproject.git
   cd Finalproject
   ```

2. Install dependencies
   ```bash
   composer install
   npm install && npm run build
   ```

3. Configure environment variables
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Setup the database
   Create a database manually in MySQL (e.g., `followup_system`), then run:
   ```bash
   php artisan migrate
   php artisan db:seed --class=DemoDataSeeder
   ```

5. Start the server
   ```bash
   php artisan serve
   ```
   > Access the application at: `http://localhost:8000`

---

## Accessing the System

### Clinician Dashboard (Main Demo Interface)
- URL: `http://localhost:8000/doctor/dashboard`
- Login: `clinician@hospital.ke`
- Password: `password`

Features to Test:
- View patient follow-up submissions.
- Click "View Pipeline" to see the middleware trace.
- Review clinical data and EMR sync status.
- Click "View in EMR" to see the integrated record.

### Patient Submission Form
- URL: `http://localhost:8000/patient/followup/create`
- Login: `patient@test.ke`
- Password: `password`

> Note: Submit a test follow-up to generate a new pipeline trace.

---

## Key Features to Demonstrate

### Middleware Pipeline Trace
- 6 Stages: Authentication, Validation, Persistence, Transformation, EMR Sync.
- Real-time execution metrics.
- Complete audit trail.

### Security Layer
- JWT authentication.
- TLS encryption.
- SQL injection protection.
- Rate limiting.

### Data Validation
- Schema validation.
- Business rules engine.
- Clinical safety checks.

### FHIR Transformation
- JSON to FHIR R4 conversion.
- SNOMED CT concept mapping.
- HL7 standards compliance.

### EMR Integration
- OpenMRS / KenyaEMR sync.
- Automatic retry on failure.
- Observation UUID tracking.

---

## Testing the Pipeline

1. Login as a clinician.
2. Navigate to "Follow-Up Submissions".
3. Click on any submission (e.g., "Ifay Test").
4. Click "View Pipeline Trace".
5. Observe all 6 middleware stages with timestamps and metadata.
6. Click "View in EMR" to see the integrated observation.

---

## Troubleshooting

| Issue | Solution |
| :--- | :--- |
| Database connection failed | Verify MySQL is running and credentials in `.env` are correct. |
| "Class not found" errors | Run `composer dump-autoload` in your terminal. |
| Pipeline shows EMR sync failed | This is expected if a mock EMR is not configured. The system queues for retry—data is safely stored in the local database. |

---

## Project Structure

```text
app/
├── Services/
│   ├── FollowUpPipelineService.php      # Main middleware orchestration
│   ├── EMRService.php                   # EMR sync handler
│   └── TriageClassificationService.php  # Urgency calculation
├── Models/
│   ├── FollowUpSubmission.php           # Submission model
│   └── PipelineLog.php                  # Pipeline trace model
└── Http/
    └── Controllers/
        └── Doctor/
            └── FollowUpController.php   # Clinician interface
```

---

## Technical Stack

- Backend: Laravel 11.x
- Frontend: Blade Templates, Tailwind CSS
- Database: MySQL 8.0
- Standards: FHIR R4, HL7, SNOMED CT
- Security: CSRF, TLS 1.3, AES-256 encryption

---

## Contact & Support

For issues or questions during evaluation, please open an issue on GitHub:
- GitHub Issues: [https://github.com/murageischeeks/Finalproject/issues](https://github.com/murageischeeks/Finalproject/issues)
