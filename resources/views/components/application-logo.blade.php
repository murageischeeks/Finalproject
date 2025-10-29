{{-- application-logo: show your image file (logo.jpeg in public/) --}}
<img src="{{ asset('logo.jpeg') }}" alt="{{ config('app.name') }}" {{ $attributes->merge(['class' => 'w-16 h-16 object-contain']) }}>
