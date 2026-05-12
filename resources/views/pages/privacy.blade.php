@extends('layouts.public')

@section('title', 'Privacy Policy')

@section('content')
<div class="bg-slate-50 border-b border-slate-200 py-12">
    <div class="max-w-4xl mx-auto px-6">
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Privacy Policy</h1>
        <p class="text-slate-600">Last updated: {{ date('F d, Y') }}</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-6 py-12 prose prose-slate">
    <h3>1. Introduction</h3>
    <p>BleakHospital takes your privacy and the security of your medical data very seriously. This policy outlines how we collect, use, and protect your personal and clinical information when you use our digital platform.</p>

    <h3>2. Data Collection</h3>
    <p>We collect information you provide directly to us when registering for an account, submitting triage follow-ups, and communicating with medical staff. This includes personally identifiable information (PII) and protected health information (PHI).</p>

    <h3>3. Use of Information</h3>
    <p>Your data is used strictly for clinical care coordination. Specifically, it is processed by our automated triage engine to determine urgency and synchronized with the national KenyaEMR system for continuity of care.</p>

    <h3>4. Data Security</h3>
    <p>We implement enterprise-grade security measures including immutable audit logs, encrypted transmissions, and strict role-based access control (RBAC) to prevent unauthorized access.</p>

    <h3>5. Contact Us</h3>
    <p>If you have any questions about this Privacy Policy, please contact our Data Protection Officer through the secure portal.</p>
</div>
@endsection
