<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndUsersSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@hups.pl'],
            [
                'name' => 'Administrator',
                'email_verified_at' => now(),
                'password' => Hash::make('12hups34'), // zmień później na bezpieczne
            ]
        );

        User::updateOrCreate(
            ['email' => 'test@hups.pl'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => Hash::make('test123'),
            ]
        );
    }
}