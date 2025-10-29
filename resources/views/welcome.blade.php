<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'MyApp') }}</title>

    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body class="antialiased bg-gradient-to-br from-indigo-100 via-white to-indigo-50 min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="flex items-center justify-between px-8 py-4 bg-white shadow">
        <div class="text-2xl font-bold text-indigo-700">
            {{ config('app.name', 'MyApp') }}
        </div>
        <div class="space-x-4">
            <a href="{{ route('login') }}" 
               class="px-5 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow hover:bg-indigo-500 transition">
                Login
            </a>
            <a href="{{ route('register') }}" 
               class="px-5 py-2 border border-indigo-600 text-indigo-600 font-semibold rounded-lg hover:bg-indigo-50 transition">
                Register
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="flex-1 flex flex-col items-center justify-center text-center px-6">
        <h1 class="text-5xl sm:text-6xl font-extrabold text-gray-800 leading-tight">
            Welcome to <span class="text-indigo-600">{{ config('app.name', 'MyApp') }}</span>
        </h1>
        <p class="mt-6 text-lg sm:text-xl text-gray-600 max-w-2xl">
            A modern healthcare management system where doctors and patients can connect seamlessly.  
            Secure, fast, and built for your needs.
        </p>
        <div class="mt-8 flex space-x-4">
            <a href="{{ route('login') }}" 
               class="px-8 py-3 bg-indigo-600 text-white text-lg font-semibold rounded-xl shadow-lg hover:bg-indigo-500 transition">
                Get Started
            </a>
            <a href="{{ route('register') }}" 
               class="px-8 py-3 border border-indigo-600 text-indigo-600 text-lg font-semibold rounded-xl hover:bg-indigo-50 transition">
                Join Us
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-6 bg-white border-t text-center text-gray-500">
        © {{ date('Y') }} {{ config('app.name', 'MyApp') }}. All rights reserved.
    </footer>

</body>
</html>
