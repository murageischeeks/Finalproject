<div>
    <!-- Always remember that you are absolutely unique. Just like everyone else. - Margaret Mead -->
</div><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Panel</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">

    {{-- Navbar --}}
    <nav class="bg-blue-700 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <h1 class="text-lg font-semibold">Doctor Dashboard</h1>
            <div class="flex space-x-4">
                <a href="{{ route('doctor.dashboard') }}" class="hover:text-gray-200">Home</a>
                <a href="{{ route('doctor.availability.index') }}" class="hover:text-gray-200">Availability</a>
                <a href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="hover:text-gray-200">Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </nav>

    {{-- Main content --}}
    <main class="flex-1 max-w-7xl mx-auto w-full p-6">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-100 text-center py-3 text-sm text-gray-600">
        &copy; {{ date('Y') }} Hospital Management System. All rights reserved.
    </footer>

</body>
</html>
