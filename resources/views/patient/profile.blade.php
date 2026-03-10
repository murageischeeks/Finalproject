@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-6 text-gray-800">My Profile</h2>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- UPDATE FORM --}}
        <form method="POST" action="{{ route('patient.profile.update') }}">
            @csrf
            @method('PATCH')

            <div class="mb-4">
                <label for="name" class="block font-medium text-gray-700">Full Name</label>
                <input id="name" type="text" name="name"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       value="{{ old('name', $patient->name ?? '') }}" required>
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block font-medium text-gray-700">Email Address</label>
                <input id="email" type="email" name="email"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       value="{{ old('email', $patient->email ?? '') }}" required>
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="dob" class="block font-medium text-gray-700">Date of Birth</label>
                <input id="dob" type="date" name="dob"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       value="{{ old('dob', $patient->dob ?? '') }}">
                @error('dob')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="gender" class="block font-medium text-gray-700">Gender</label>
                <select id="gender" name="gender"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select</option>
                    <option value="male"   {{ ($patient->gender ?? '') == 'male'   ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ ($patient->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other"  {{ ($patient->gender ?? '') == 'other'  ? 'selected' : '' }}>Other</option>
                </select>
                @error('gender')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block font-medium text-gray-700">New Password <span class="text-gray-400 font-normal">(leave blank to keep current)</span></label>
                <input id="password" type="password" name="password"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block font-medium text-gray-700">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                Update Profile
            </button>
        </form>

        {{-- DELETE FORM — outside update form ──────────────── --}}
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-sm font-semibold text-red-600 mb-3">Danger Zone</h3>
            <form method="POST" action="{{ route('patient.profile.destroy') }}">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition"
                        onclick="return confirm('Are you sure you want to delete your account? This cannot be undone.');">
                    Delete Account
                </button>
            </form>
        </div>

    </div>
</div>
@endsection