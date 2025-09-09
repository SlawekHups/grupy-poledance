<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Uruchamiaj seeder ról/użytkowników tylko raz
        $this->call([
            RolesAndUsersSeeder::class,
            GroupsSeeder::class,
            // UsersSeeder::class, // Wyłączony: nie ładuj przykładowych użytkowników do grup
        ]);
        // User::factory(10)->create();
    }
}
