# Interoperable Patient Follow-Up System
## Middleware-Based EMR Integration

---

## System Overview
This system enables patients to submit post-consultation follow-ups via 
mobile/web forms, with middleware handling validation, transformation, and 
synchronization to hospital EMR systems (OpenMRS/KenyaEMR).

**Core Innovation:** Middleware pipeline providing complete observability 
into data processing, validation, and EMR integration.

---

## Prerequisites

- PHP 8.1 or higher
- Composer 2.x
- MySQL 8.0 or higher
- Node.js 16+ & NPM
- Git

---

## Installation Steps

1. Clone repository
```bash
git clone https://github.com/murageischeeks/Finalproject.git
cd Finalproject
```

2. Install dependencies
```bash
composer install
npm install && npm run build
```

3. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Setup database
Create database manually in MySQL (e.g., `followup_system`)
```bash
php artisan migrate
php artisan db:seed --class=DemoDataSeeder
```

5. Start server
```bash
php artisan serve
```
Access at: `http://localhost:8000`

---

## Accessing the System

### Clinician Dashboard (Main Demo Interface)
URL: `http://localhost:8000/doctor/dashboard`
Login: `clinician@hospital.ke` / `password`

Features to Test:
- View patient follow-up submissions
- Click "View Pipeline" to see middleware trace
- Review clinical data and EMR sync status
- Click "View in EMR" to see integrated record

### Patient Submission Form
URL: `http://localhost:8000/patient/followup/create`
Login: `patient@test.ke` / `password`

Submit a test follow-up to generate new pipeline trace.

---

## Key Features to Demonstrate

✅ **Middleware Pipeline Trace** 
   - 6 stages: Authentication, Validation, Persistence, Transformation, EMR Sync
   - Real-time execution metrics
   - Complete audit trail

✅ **Security Layer** 
   - JWT authentication
   - TLS encryption
   - SQL injection protection
   - Rate limiting

✅ **Data Validation** 
   - Schema validation
   - Business rules engine
   - Clinical safety checks

✅ **FHIR Transformation**
   - JSON to FHIR R4 conversion
   - SNOMED CT concept mapping
   - HL7 standards compliance

✅ **EMR Integration**
   - OpenMRS/KenyaEMR sync
   - Automatic retry on failure
   - Observation UUID tracking

---

## Testing the Pipeline

1. Login as clinician
2. Navigate to "Follow-Up Submissions"
3. Click on any submission (e.g., "Ifay Test")
4. Click "View Pipeline Trace"
5. Observe all 6 middleware stages with timestamps and metadata
6. Click "View in EMR" to see integrated observation

---

## Troubleshooting

**Issue:** Database connection failed
**Solution:** Verify MySQL is running and credentials in `.env` are correct

**Issue:** "Class not found" errors
**Solution:** Run `composer dump-autoload`

**Issue:** Pipeline shows EMR sync failed
**Solution:** This is expected if mock EMR is not configured. System queues 
for retry - data is safe in local database.

---

## Project Structure

```
app/
├── Services/
│   ├── FollowUpPipelineService.php  # Main middleware orchestration
│   ├── EMRService.php               # EMR sync handler
│   └── TriageClassificationService.php            # Urgency calculation
├── Models/
│   ├── FollowUpSubmission.php       # Submission model
│   └── PipelineLog.php              # Pipeline trace model
└── Http/
    └── Controllers/
        └── Doctor/
            └── FollowUpController.php  # Clinician interface
```

---

## Technical Stack

- **Backend:** Laravel 11.x
- **Frontend:** Blade Templates, Tailwind CSS
- **Database:** MySQL 8.0
- **Standards:** FHIR R4, HL7, SNOMED CT
- **Security:** CSRF, TLS 1.3, AES-256 encryption

---

## Contact & Support

For issues or questions during evaluation:
- GitHub Issues: https://github.com/murageischeeks/Finalproject/issues
