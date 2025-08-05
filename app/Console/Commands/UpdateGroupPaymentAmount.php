<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Group;
use App\Models\User;
use App\Models\Payment;
use Carbon\Carbon;

class UpdateGroupPaymentAmount extends Command
{
    protected $signature = 'payments:update-group-amount {group_id} {amount} {--scope=current_month : Zakres zmian (current_month, future_months, all_months)}';
    protected $description = 'Aktualizuje kwotę płatności dla wszystkich użytkowników w grupie';

    public function handle()
    {
        $groupId = (int) $this->argument('group_id');
        $newAmount = (float) $this->argument('amount');
        $scope = $this->option('scope');

        $group = Group::find($groupId);
        
        if (!$group) {
            $this->error('Grupa nie została znaleziona');
            return 1;
        }

        $this->info("Aktualizuję kwotę płatności dla grupy: {$group->name}");
        $this->info("Nowa kwota: {$newAmount} zł");
        $this->info("Zakres: {$scope}");

        if (!$this->confirm('Czy na pewno chcesz kontynuować?')) {
            $this->info('Operacja anulowana');
            return 0;
        }

        $users = $group->users()->where('is_active', true)->get();
        $updatedUsers = 0;
        $updatedPayments = 0;

        $this->info("Znaleziono {$users->count()} aktywnych użytkowników w grupie");

        foreach ($users as $user) {
            $this->line("Aktualizuję użytkownika: {$user->name} ({$user->email})");
            
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
                default:
                    $this->error("Nieznany zakres: {$scope}");
                    return 1;
            }

            $paymentCount = $paymentQuery->update(['amount' => $newAmount]);
            $updatedPayments += $paymentCount;
            
            $this->line("  - Zaktualizowano {$paymentCount} płatności");
        }

        $this->info('');
        $this->info('✅ Operacja zakończona pomyślnie!');
        $this->info("Zaktualizowano kwotę dla {$updatedUsers} użytkowników");
        $this->info("Zaktualizowano {$updatedPayments} płatności");

        return 0;
    }
} 