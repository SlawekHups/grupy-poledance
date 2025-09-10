<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Payment;
use Carbon\Carbon;

class GenerateMissingPayments extends Command
{
    protected $signature = 'payments:generate-missing {--month= : Miesiąc w formacie Y-m (np. 2025-08)} {--all-months : Generuj płatności dla wszystkich miesięcy od początku roku}';
    protected $description = 'Generuje brakujące płatności dla użytkowników dodanych w trakcie miesiąca';

    public function handle()
    {
        $month = $this->option('month') ?: Carbon::now()->format('Y-m');
        $allMonths = $this->option('all-months');

        if ($allMonths) {
            $this->generateForAllMonths();
        } else {
            $this->generateForSpecificMonth($month);
        }

        return 0;
    }

    /**
     * Generuje płatności dla konkretnego miesiąca
     */
    private function generateForSpecificMonth(string $month): void
    {
        $this->info("Generuję brakujące płatności za miesiąc: {$month}");

        $activeUsers = User::where('is_active', true)
            ->where('id', '!=', 1) // Pomijamy admina
            ->where(function ($query) {
                // Użytkownicy z aktywnymi grupami ID >= 2 (status = 'active')
                $query->whereHas('groups', function ($groupQuery) {
                    $groupQuery->where('group_id', '>=', 2)
                              ->where('status', 'active');
                })
                // LUB użytkownicy z legacy group_id >= 2 i aktywną grupą
                ->orWhereHas('group', function ($groupQuery) {
                    $groupQuery->where('id', '>=', 2)
                              ->where('status', 'active');
                });
            })
            ->get();

        $generatedCount = 0;
        $skippedCount = 0;

        foreach ($activeUsers as $user) {
            // Sprawdź czy użytkownik ma ustawioną kwotę
            if (empty($user->amount) || $user->amount <= 0) {
                $this->warn("Pominięto użytkownika {$user->name} - brak kwoty miesięcznej");
                $skippedCount++;
                continue;
            }

            // Sprawdź czy już istnieje płatność na ten miesiąc
            $exists = Payment::where('user_id', $user->id)
                ->where('month', $month)
                ->exists();

            if (!$exists) {
                // Sprawdź czy użytkownik był aktywny w tym miesiącu
                if ($this->wasUserActiveInMonth($user, $month)) {
                    Payment::create([
                        'user_id' => $user->id,
                        'amount' => $user->amount,
                        'month' => $month,
                        'paid' => false,
                        'payment_link' => null,
                    ]);

                    $this->info("✅ Dodano płatność dla {$user->name} - {$month} ({$user->amount} zł)");
                    $generatedCount++;
                } else {
                    $this->warn("⚠️ Pominięto użytkownika {$user->name} - nie był aktywny w {$month}");
                    $skippedCount++;
                }
            } else {
                $this->line("ℹ️ Użytkownik {$user->name} już ma płatność za {$month}");
            }
        }

        $this->newLine();
        $this->info("=== PODSUMOWANIE ===");
        $this->info("Miesiąc: {$month}");
        $this->info("Wygenerowano płatności: {$generatedCount}");
        $this->info("Pominięto: {$skippedCount}");
        $this->info("Łącznie przetworzono: " . ($generatedCount + $skippedCount));
    }

    /**
     * Generuje płatności dla wszystkich miesięcy od początku roku
     */
    private function generateForAllMonths(): void
    {
        $this->info("Generuję brakujące płatności dla wszystkich miesięcy od początku roku");

        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $totalGenerated = 0;

        for ($month = 1; $month <= $currentMonth; $month++) {
            $monthString = sprintf('%04d-%02d', $currentYear, $month);
            $this->newLine();
            $this->info("=== MIESIĄC: {$monthString} ===");
            
            $generated = $this->generateForMonth($monthString);
            $totalGenerated += $generated;
        }

        $this->newLine();
        $this->info("=== PODSUMOWANIE CAŁKOWITE ===");
        $this->info("Łącznie wygenerowano płatności: {$totalGenerated}");
    }

    /**
     * Generuje płatności dla konkretnego miesiąca (pomocnicza metoda)
     */
    private function generateForMonth(string $month): int
    {
        $activeUsers = User::where('is_active', true)
            ->where('id', '!=', 1)
            ->where(function ($query) {
                // Użytkownicy z aktywnymi grupami ID >= 2 (status = 'active')
                $query->whereHas('groups', function ($groupQuery) {
                    $groupQuery->where('group_id', '>=', 2)
                              ->where('status', 'active');
                })
                // LUB użytkownicy z legacy group_id >= 2 i aktywną grupą
                ->orWhereHas('group', function ($groupQuery) {
                    $groupQuery->where('id', '>=', 2)
                              ->where('status', 'active');
                });
            })
            ->get();

        $generatedCount = 0;

        foreach ($activeUsers as $user) {
            if (empty($user->amount) || $user->amount <= 0) {
                continue;
            }

            $exists = Payment::where('user_id', $user->id)
                ->where('month', $month)
                ->exists();

            if (!$exists && $this->wasUserActiveInMonth($user, $month)) {
                Payment::create([
                    'user_id' => $user->id,
                    'amount' => $user->amount,
                    'month' => $month,
                    'paid' => false,
                    'payment_link' => null,
                ]);

                $generatedCount++;
            }
        }

        $this->info("Wygenerowano: {$generatedCount} płatności");
        return $generatedCount;
    }

    /**
     * Sprawdza czy użytkownik był aktywny w danym miesiącu
     */
    private function wasUserActiveInMonth(User $user, string $month): bool
    {
        $monthDate = Carbon::createFromFormat('Y-m', $month);
        $startOfMonth = $monthDate->copy()->startOfMonth();
        $endOfMonth = $monthDate->copy()->endOfMonth();

        // Sprawdź czy użytkownik został dodany przed końcem miesiąca
        if ($user->created_at->gt($endOfMonth)) {
            return false;
        }

        // Sprawdź czy użytkownik był aktywny w tym miesiącu
        // Można dodać dodatkową logikę sprawdzania obecności w zajęciach
        return true;
    }
}
