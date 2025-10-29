<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'bleak@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('bleak'), // change to a secure password
            ]
        );
    }
}
