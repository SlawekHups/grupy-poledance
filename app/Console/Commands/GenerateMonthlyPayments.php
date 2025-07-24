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
}
