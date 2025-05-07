<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;

class GroupsSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'Bez grupy',
            'Poniedziałek 18:00',
            'Poniedziałek 19:00',
            'Poniedziałek 20:00',
            'Wtorek 18:00',
            'Wtorek 19:00',
            'Wtorek 20:00',
            'Środa 18:00',
            'Środa 19:00',
            'Środa 20:00',
            'Czwartek 18:00',
            'Czwartek 19:00',
            'Czwartek 20:00',
        ];

        foreach ($groups as $name) {
            Group::firstOrCreate(['name' => $name]);
        }
    }
}