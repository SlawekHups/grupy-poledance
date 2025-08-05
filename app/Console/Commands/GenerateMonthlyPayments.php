<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Payment;
use Carbon\Carbon;


class GenerateMonthlyPayments extends Command
{
    protected $signature = 'payments:generate';
    protected $description = 'Tworzy płatności dla aktywnych użytkowników na dany miesiąc';

    public function handle()
    {
        $month = Carbon::now()->format('Y-m');

        $activeUsers = User::where('is_active', true)
            ->where('id', '!=', 1)
            ->get();

        foreach ($activeUsers as $user) {
            // sprawdź, czy użytkownik ma ustawioną kwotę
            if (empty($user->amount) || $user->amount <= 0) {
                $this->warn("Pominięto użytkownika {$user->name} - brak kwoty miesięcznej");
                continue;
            }

            // sprawdź, czy już istnieje płatność na ten miesiąc
            $exists = Payment::where('user_id', $user->id)
                ->where('month', $month)
                ->exists();

            if (! $exists) {
                Payment::create([
                    'user_id' => $user->id,
                    'amount' => $user->amount,
                    'month' => $month,
                    'link' => null, // lub generuj tutaj np. przez API Przelewy24
                ]);

                $this->info("Dodano płatność dla {$user->name}");
            }
        }

        $this->info('Gotowe.');
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
