<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class RolesAndUsersSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@hups.pl'],
            [
                'name' => 'Administrator',
                'email_verified_at' => now(),
                'password' => '12hups34', // plain; zostanie zhashowane przez mutator w modelu
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'test@hups.pl'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => 'test123', // plain; zostanie zhashowane przez mutator
                'role' => 'user',
            ]
        );
    }
}