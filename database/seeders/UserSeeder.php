<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ])->assignRole('admin');

        // Create tutor user
        User::create([
            'name' => 'Tutor User',
            'email' => 'tutor@example.com',
            'password' => Hash::make('password'),
        ])->assignRole('tutor');

        // Create student user
        User::create([
            'name' => 'Student User',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
        ])->assignRole('student');
    }
}

// to run this seeder use php artisan db:seed --class=UserSeeder
