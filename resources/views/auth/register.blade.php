<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="{{ url('/') }}">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" class="block mt-1 w-full"
                              type="text"
                              name="name"
                              :value="old('name')"
                              required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full"
                              type="email"
                              name="email"
                              :value="old('email')"
                              required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full"
                              type="password"
                              name="password"
                              required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                              type="password"
                              name="password_confirmation"
                              required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Role selection -->
            <div class="mt-4">
                <x-input-label for="role" :value="__('Register as')" />
                <select id="role" name="role" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    <option value="patient" {{ old('role') === 'patient' ? 'selected' : '' }}>Patient</option>
                    <option value="doctor" {{ old('role') === 'doctor' ? 'selected' : '' }}>Doctor</option>
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <!-- Doctor-only fields -->
            <div id="doctor-fields" class="mt-4 hidden">

                <div>
                    <x-input-label for="license_number" :value="__('License Number')" />
                    <x-text-input id="license_number" class="block mt-1 w-full"
                                  type="text"
                                  name="license_number"
                                  :value="old('license_number')" />
                    <x-input-error :messages="$errors->get('license_number')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="department" :value="__('Department')" />
                    <x-text-input id="department" class="block mt-1 w-full"
                                  type="text"
                                  name="department"
                                  :value="old('department')" />
                    <x-input-error :messages="$errors->get('department')" class="mt-2" />
                </div>

                <!-- Specialization Dropdown + Other -->
                <div class="mt-4">
                    <x-input-label for="specialization" :value="__('Specialization')" />

                    <select id="specialization_select" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" onchange="toggleOtherSpecialization()">
                        <option value="">Select Specialization</option>
                        <option value="Cardiology" {{ old('specialization') === 'Cardiology' ? 'selected' : '' }}>Cardiology</option>
                        <option value="Neurology" {{ old('specialization') === 'Neurology' ? 'selected' : '' }}>Neurology</option>
                        <option value="Pediatrics" {{ old('specialization') === 'Pediatrics' ? 'selected' : '' }}>Pediatrics</option>
                        <option value="General" {{ old('specialization') === 'General' ? 'selected' : '' }}>General</option>
                        <option value="Other" {{ !in_array(old('specialization'), ['Cardiology','Neurology','Pediatrics','General']) && old('specialization') ? 'selected' : '' }}>Other</option>
                    </select>

                    <input id="specialization_other" type="text" name="specialization" class="block mt-2 w-full border-gray-300 rounded-md shadow-sm"
                           placeholder="Enter your specialization"
                           value="{{ !in_array(old('specialization'), ['Cardiology','Neurology','Pediatrics','General']) ? old('specialization') : '' }}"
                           style="display: {{ !in_array(old('specialization'), ['Cardiology','Neurology','Pediatrics','General']) && old('specialization') ? 'block' : 'none' }};">

                    <x-input-error :messages="$errors->get('specialization')" class="mt-2" />
                </div>

            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-primary-button class="ms-4">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>

<!-- Script to toggle doctor fields and other specialization -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('role');
    const doctorFields = document.getElementById('doctor-fields');
    const specSelect = document.getElementById('specialization_select');
    const specOther = document.getElementById('specialization_other');

    function toggleDoctorFields() {
        if (roleSelect.value === 'doctor') {
            doctorFields.classList.remove('hidden');
        } else {
            doctorFields.classList.add('hidden');
        }
    }

    function toggleOtherSpecialization() {
        if (specSelect.value === 'Other') {
            specOther.style.display = 'block';
            specOther.focus();
        } else {
            specOther.style.display = 'none';
            specOther.value = specSelect.value;
        }
    }

    roleSelect.addEventListener('change', toggleDoctorFields);
    specSelect.addEventListener('change', toggleOtherSpecialization);

    toggleDoctorFields();
    toggleOtherSpecialization();
});
</script>
