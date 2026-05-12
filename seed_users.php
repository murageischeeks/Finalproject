<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

$faker = Faker::create();
$password = Hash::make('password123');

echo "Seeding 5 patients...\n";
for ($i = 0; $i < 5; $i++) {
    User::create([
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password,
        'role' => 'patient',
    ]);
}

echo "Seeding 20 doctors...\n";
for ($i = 0; $i < 20; $i++) {
    User::create([
        'name' => 'Dr. ' . $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'password' => $password,
        'role' => 'doctor',
    ]);
}

echo "✅ Seeded successfully.\n";
echo "Total Users: " . User::count() . "\n";
echo "Total Doctors: " . User::where('role', 'doctor')->count() . "\n";
echo "Total Patients: " . User::where('role', 'patient')->count() . "\n";
