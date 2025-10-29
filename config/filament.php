<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Broadcasting
    |--------------------------------------------------------------------------
    |
    | Optional: Uncomment and configure this if you want Filament to use Echo /
    | Pusher for real-time notifications, e.g., when a doctor or patient
    | receives updates.
    |
    */

    'broadcasting' => [

        // 'echo' => [
        //     'broadcaster' => 'pusher',
        //     'key' => env('VITE_PUSHER_APP_KEY'),
        //     'cluster' => env('VITE_PUSHER_APP_CLUSTER'),
        //     'wsHost' => env('VITE_PUSHER_HOST'),
        //     'wsPort' => env('VITE_PUSHER_PORT'),
        //     'wssPort' => env('VITE_PUSHER_PORT'),
        //     'authEndpoint' => '/broadcasting/auth',
        //     'disableStats' => true,
        //     'encrypted' => true,
        //     'forceTLS' => true,
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Where Filament should store uploaded files. "public" is fine for most
    | cases unless you use a custom disk for your admin uploads.
    |
    */

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Path Prefix
    |--------------------------------------------------------------------------
    |
    | This sets the URL prefix where your Filament admin panel is accessible.
    | Example: "admin" makes your panel available at https://yourapp.com/admin
    |
    */

    'path' => env('FILAMENT_PATH', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | Auth Guard
    |--------------------------------------------------------------------------
    |
    | The authentication guard used for the Filament admin panel.
    | You can use a custom "admin" guard if you have one.
    |
    */

    'auth' => [
        'guard' => 'web',
    ],

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | The middleware stack Filament uses for its admin routes.
    | "auth" ensures only logged-in users can access the panel.
    |
    */

    'middleware' => [
        'web',
        'auth',
        \Filament\Http\Middleware\Authenticate::class,
        \Filament\Http\Middleware\DispatchServingFilamentEvent::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Branding
    |--------------------------------------------------------------------------
    |
    | You can set your admin panel name, logo, and colors here.
    |
    */

    'brand' => [
        'name' => env('APP_NAME', 'Hospital Admin Panel'),
        'logo' => null, // You can add your logo in public/images/logo.png
        'colors' => [
            'primary' => '#2563eb', // blue-600
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    |
    | Controls sidebar order and visibility for Filament resources.
    |
    */

    'navigation' => [
        'sort' => 0,
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire Loading Delay
    |--------------------------------------------------------------------------
    |
    | This controls when loading spinners appear in the UI.
    |
    */

    'livewire_loading_delay' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Cache Path
    |--------------------------------------------------------------------------
    |
    | Where Filament stores optimized component registration caches.
    |
    */

    'cache_path' => base_path('bootstrap/cache/filament'),

    /*
    |--------------------------------------------------------------------------
    | System Route Prefix
    |--------------------------------------------------------------------------
    |
    | Used for background system routes like exports and imports.
    |
    */

    'system_route_prefix' => 'filament-system',

];
