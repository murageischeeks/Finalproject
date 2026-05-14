@extends('layouts.app')

@section('content')
<div class="container py-10">
    <div class="card p-8 text-center max-w-lg mx-auto">
        <h2 class="text-2xl font-bold text-surface-900 mb-2">Admin Dashboard</h2>
        <p class="text-surface-500">Welcome, {{ Auth::check() ? Auth::user()->name : 'Guest Administrator' }}!</p>
        <div class="mt-6 p-4 bg-amber-50 text-amber-700 rounded-lg text-sm border border-amber-100">
            ⚠️ This dashboard is currently in <strong>Demo Mode</strong> and is accessible without a password.
        </div>
    </div>
</div>
@endsection
