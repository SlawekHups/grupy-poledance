<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Group;
use Carbon\Carbon;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Rozpoczynam dodawanie użytkowników do grup...');

        // Pobierz wszystkie grupy (pomijając "Bez grupy")
        $groups = Group::where('id', '!=', 1)->get();

        if ($groups->isEmpty()) {
            $this->command->warn('❌ Brak grup w systemie! Najpierw uruchom GroupsSeeder.');
            return;
        }

        $totalUsers = 0;

        foreach ($groups as $group) {
            $this->command->info("📝 Dodaję użytkowników do grupy: {$group->name}");

            // Generuj 3 użytkowników dla każdej grupy
            for ($i = 1; $i <= 3; $i++) {
                $userData = $this->generateUserData($group, $i);
                
                // Sprawdź czy użytkownik już istnieje
                $existingUser = User::where('email', $userData['email'])->first();
                
                if ($existingUser) {
                    $this->command->warn("  ⚠️ Użytkownik {$userData['email']} już istnieje, pomijam");
                    continue;
                }

                // Utwórz użytkownika
                $user = User::create($userData);
                
                $this->command->info("  ✅ Dodano: {$user->name} ({$user->email})");
                $totalUsers++;
            }
        }

        $this->command->info("🎉 Zakończono! Dodano {$totalUsers} użytkowników.");
        $this->command->info("💡 Uruchom 'php artisan payments:generate-missing' aby wygenerować płatności dla nowych użytkowników.");
    }

    /**
     * Generuje dane użytkownika na podstawie grupy
     */
    private function generateUserData(Group $group, int $userNumber): array
    {
        // KONKRETNE DANE UŻYTKOWNIKÓW - ŁATWO EDYTOWALNE
        $usersData = [
            // Grupa 1: Poniedziałek 18:00
            'Poniedziałek 18:00' => [
                1 => ['name' => 'Anna Kowalska', 'email' => 'anna.kowalska@hups.pl', 'phone' => null],
                2 => ['name' => 'Marek Nowak', 'email' => 'marek.nowak@hups.pl', 'phone' => null],
                3 => ['name' => 'Kasia Wiśniewska', 'email' => 'kasia.wisniewska@hups.pl', 'phone' => null],
            ],
            // Grupa 2: Poniedziałek 19:00
            'Poniedziałek 19:00' => [
                1 => ['name' => 'Piotr Kowalczyk', 'email' => 'piotr.kowalczyk@hups.pl', 'phone' => null],
                2 => ['name' => 'Magda Zielińska', 'email' => 'magda.zielinska@hups.pl', 'phone' => null],
                3 => ['name' => 'Tomek Jankowski', 'email' => 'tomek.jankowski@hups.pl', 'phone' => null],
            ],
            // Grupa 3: Poniedziałek 20:00
            'Poniedziałek 20:00' => [
                1 => ['name' => 'Agnieszka Lewandowska', 'email' => 'agnieszka.lewandowska@hups.pl', 'phone' => null],
                2 => ['name' => 'Michał Dąbrowski', 'email' => 'michal.dabrowski@hups.pl', 'phone' => null],
                3 => ['name' => 'Ewa Wójcik', 'email' => 'ewa.wojcik@hups.pl', 'phone' => null],
            ],
            // Grupa 4: Wtorek 18:00
            'Wtorek 18:00' => [
                1 => ['name' => 'Jan Kamiński', 'email' => 'jan.kaminski@hups.pl', 'phone' => null],
                2 => ['name' => 'Monika Kaczmarek', 'email' => 'monika.kaczmarek@hups.pl', 'phone' => null],
                3 => ['name' => 'Adam Piotrowski', 'email' => 'adam.piotrowski@hups.pl', 'phone' => null],
            ],
            // Grupa 5: Wtorek 19:00
            'Wtorek 19:00' => [
                1 => ['name' => 'Karolina Grabowski', 'email' => 'karolina.grabowski@hups.pl', 'phone' => null],
                2 => ['name' => 'Łukasz Michalski', 'email' => 'lukasz.michalski@hups.pl', 'phone' => null],
                3 => ['name' => 'Natalia Nowicka', 'email' => 'natalia.nowicka@hups.pl', 'phone' => null],
            ],
            // Grupa 6: Wtorek 20:00
            'Wtorek 20:00' => [
                1 => ['name' => 'Marta Adamczyk', 'email' => 'marta.adamczyk@hups.pl', 'phone' => null],
                2 => ['name' => 'Krzysztof Sikora', 'email' => 'krzysztof.sikora@hups.pl', 'phone' => null],
                3 => ['name' => 'Aleksandra Mazur', 'email' => 'aleksandra.mazur@hups.pl', 'phone' => null],
            ],
            // Grupa 7: Środa 18:00
            'Środa 18:00' => [
                1 => ['name' => 'Dawid Pawlak', 'email' => 'dawid.pawlak@hups.pl', 'phone' => null],
                2 => ['name' => 'Joanna Król', 'email' => 'joanna.krol@hups.pl', 'phone' => null],
                3 => ['name' => 'Marcin Wieczorek', 'email' => 'marcin.wieczorek@hups.pl', 'phone' => null],
            ],
            // Grupa 8: Środa 19:00
            'Środa 19:00' => [
                1 => ['name' => 'Marta Jaworska', 'email' => 'marta.jaworska@hups.pl', 'phone' => null],
                2 => ['name' => 'Krzysztof Malinowski', 'email' => 'krzysztof.malinowski@hups.pl', 'phone' => null],
                3 => ['name' => 'Aleksandra Lis', 'email' => 'aleksandra.lis@hups.pl', 'phone' => null],
            ],
            // Grupa 9: Środa 20:00
            'Środa 20:00' => [
                1 => ['name' => 'Tomasz Kowal', 'email' => 'tomasz.kowal@hups.pl', 'phone' => null],
                2 => ['name' => 'Ewa Sobczak', 'email' => 'ewa.sobczak@hups.pl', 'phone' => null],
                3 => ['name' => 'Marek Czerwiński', 'email' => 'marek.czerwinski@hups.pl', 'phone' => null],
            ],
            // Grupa 10: Czwartek 18:00
            'Czwartek 18:00' => [
                1 => ['name' => 'Anna Szymańska', 'email' => 'anna.szymanska@hups.pl', 'phone' => null],
                2 => ['name' => 'Piotr Woźniak', 'email' => 'piotr.wozniak@hups.pl', 'phone' => null],
                3 => ['name' => 'Kasia Kozłowska', 'email' => 'kasia.kozlowska@hups.pl', 'phone' => null],
            ],
            // Grupa 11: Czwartek 19:00
            'Czwartek 19:00' => [
                1 => ['name' => 'Marek Jasiński', 'email' => 'marek.jasinski@hups.pl', 'phone' => null],
                2 => ['name' => 'Magda Stępień', 'email' => 'magda.stepien@hups.pl', 'phone' => null],
                3 => ['name' => 'Tomek Górski', 'email' => 'tomek.gorski@hups.pl', 'phone' => null],
            ],
            // Grupa 12: Czwartek 20:00
            'Czwartek 20:00' => [
                1 => ['name' => 'Agnieszka Róg', 'email' => 'agnieszka.rog@hups.pl', 'phone' => null],
                2 => ['name' => 'Michał Makowski', 'email' => 'michal.makowski@hups.pl', 'phone' => null],
                3 => ['name' => 'Ewa Kaczmarczyk', 'email' => 'ewa.kaczmarczyk@hups.pl', 'phone' => null],
            ],
        ];

        // Pobierz dane dla konkretnej grupy i numeru użytkownika
        $groupData = $usersData[$group->name] ?? null;
        
        if (!$groupData || !isset($groupData[$userNumber])) {
            // Fallback - jeśli brak danych dla grupy
            return [
                'name' => "Użytkownik {$userNumber}",
                'email' => "user{$userNumber}.{$group->id}@hups.pl",
                'phone' => '+48' . rand(100000000, 999999999),
                'group_id' => $group->id,
                'amount' => config('app.default_user_amount'),
                'is_active' => true,
                'role' => 'user',
                'joined_at' => Carbon::now()->subDays(rand(1, 30)),
                'password' => null,
            ];
        }

        $userData = $groupData[$userNumber];

        return [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'phone' => $userData['phone'],
            'group_id' => $group->id,
            'amount' => config('app.default_user_amount'), // Domyślna kwota miesięczna
            'is_active' => true,
            'role' => 'user',
            'joined_at' => Carbon::now()->subDays(rand(1, 30)), // Losowa data dołączenia
            'password' => null, // Bez hasła - użytkownik dostanie zaproszenie
        ];
    }
}
