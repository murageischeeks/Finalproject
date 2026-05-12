@extends('layouts.public')

@section('title', 'Terms of Service')

@section('content')
<div class="bg-slate-50 border-b border-slate-200 py-12">
    <div class="max-w-4xl mx-auto px-6">
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Terms of Service</h1>
        <p class="text-slate-600">Last updated: {{ date('F d, Y') }}</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-6 py-12 prose prose-slate">
    <h3>1. Acceptance of Terms</h3>
    <p>By accessing or using the BleakHospital digital portal, you agree to be bound by these Terms of Service. If you do not agree, please do not use the service.</p>

    <h3>2. Medical Disclaimer</h3>
    <p><strong>This service is not for medical emergencies.</strong> If you are experiencing a medical emergency, call your local emergency services immediately. The automated triage system provides prioritization, not a definitive medical diagnosis.</p>

    <h3>3. User Responsibilities</h3>
    <p>You agree to provide accurate, current, and complete information during registration and when submitting medical follow-ups. You are responsible for maintaining the confidentiality of your account credentials.</p>

    <h3>4. Service Availability</h3>
    <p>While we strive for 100% uptime, the platform is provided "as is" and we do not guarantee continuous, uninterrupted access to the platform or integration with external EMR systems.</p>

    <h3>5. Modifications</h3>
    <p>We reserve the right to modify these terms at any time. Continued use of the platform after changes constitutes acceptance of the new terms.</p>
</div>
@endsection
