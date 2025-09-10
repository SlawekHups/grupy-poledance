<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Payment;
use Carbon\Carbon;

class GenerateMonthlyPayments extends Command
{
    protected $signature = 'payments:generate {--month= : Miesiąc w formacie Y-m (np. 2025-08)} {--force : Wymuś generowanie nawet jeśli płatności już istnieją}';
    protected $description = 'Tworzy płatności dla aktywnych użytkowników na dany miesiąc';

    public function handle()
    {
        $month = $this->option('month') ?: Carbon::now()->format('Y-m');
        $force = $this->option('force');

        $this->info("Generuję płatności za miesiąc: {$month}");

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
        $updatedCount = 0;

        foreach ($activeUsers as $user) {
            // Sprawdź czy użytkownik ma ustawioną kwotę
            if (empty($user->amount) || $user->amount <= 0) {
                $this->warn("Pominięto użytkownika {$user->name} - brak kwoty miesięcznej");
                $skippedCount++;
                continue;
            }

            // Sprawdź czy użytkownik był aktywny w tym miesiącu
            if (!$this->wasUserActiveInMonth($user, $month)) {
                $this->warn("Pominięto użytkownika {$user->name} - nie był aktywny w {$month}");
                $skippedCount++;
                continue;
            }

            // Sprawdź czy już istnieje płatność na ten miesiąc
            $existingPayment = Payment::where('user_id', $user->id)
                ->where('month', $month)
                ->first();

            if (!$existingPayment) {
                // Twórz nową płatność
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
                // Sprawdź czy kwota się zmieniła
                if ($existingPayment->amount != $user->amount) {
                    if ($force) {
                        $existingPayment->update(['amount' => $user->amount]);
                        $this->info("🔄 Zaktualizowano płatność dla {$user->name} - {$month} (nowa kwota: {$user->amount} zł)");
                        $updatedCount++;
                    } else {
                        $this->line("ℹ️ Użytkownik {$user->name} już ma płatność za {$month} (kwota: {$existingPayment->amount} zł, aktualna: {$user->amount} zł)");
                    }
                } else {
                    $this->line("ℹ️ Użytkownik {$user->name} już ma płatność za {$month} ({$existingPayment->amount} zł)");
                }
            }
        }

        $this->newLine();
        $this->info("=== PODSUMOWANIE ===");
        $this->info("Miesiąc: {$month}");
        $this->info("Dodano nowych płatności: {$generatedCount}");
        $this->info("Zaktualizowano płatności: {$updatedCount}");
        $this->info("Pominięto: {$skippedCount}");
        $this->info("Łącznie przetworzono: " . ($generatedCount + $updatedCount + $skippedCount));

        if ($generatedCount > 0 || $updatedCount > 0) {
            $this->info("💡 Użyj 'php artisan payments:generate-missing --month={$month}' aby uzupełnić brakujące płatności");
        }

        return 0;
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

        // Sprawdź czy użytkownik ma ustawioną datę dołączenia
        if ($user->joined_at) {
            $joinedDate = Carbon::parse($user->joined_at);
            if ($joinedDate->gt($endOfMonth)) {
                return false;
            }
        }

        // Sprawdź czy użytkownik był aktywny w tym miesiącu
        // Można dodać dodatkową logikę sprawdzania obecności w zajęciach
        return true;
    }

    /**
     * Aktualizuje kwotę płatności dla wszystkich użytkowników w grupie
     */
    public static function updateGroupPaymentAmount(int $groupId, float $newAmount, string $scope = 'current_month'): array
    {
        $group = \App\Models\Group::find($groupId);
        
        if (!$group) {
            return ['success' => false, 'message' => 'Grupa nie została znaleziona'];
        }

        $users = $group->users()->where('is_active', true)->get();
        $updatedUsers = 0;
        $updatedPayments = 0;

        foreach ($users as $user) {
            // Aktualizuj kwotę użytkownika
            $user->update(['amount' => $newAmount]);
            $updatedUsers++;

            // Aktualizuj płatności w zależności od zakresu
            $paymentQuery = $user->payments();

            switch ($scope) {
                case 'current_month':
                    $paymentQuery->where('month', now()->format('Y-m'));
                    break;
                case 'future_months':
                    $paymentQuery->where('month', '>=', now()->format('Y-m'));
                    break;
                case 'all_months':
                    // Wszystkie płatności
                    break;
            }

            $updatedPayments += $paymentQuery->update(['amount' => $newAmount]);
        }

        return [
            'success' => true,
            'message' => "Zaktualizowano kwotę dla {$updatedUsers} użytkowników i {$updatedPayments} płatności w grupie '{$group->name}'",
            'updated_users' => $updatedUsers,
            'updated_payments' => $updatedPayments,
            'group_name' => $group->name
        ];
    }
}
