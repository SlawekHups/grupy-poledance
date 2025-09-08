<?php

namespace App\Console\Commands;

use App\Mail\AdminDailyPaymentDigestMail;
use App\Models\AdminPaymentDigestLog;
use App\Models\Group;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAdminPaymentDigest extends Command
{
    protected $signature = 'payments:send-admin-digest {--dry-run : Nie wysyłaj maila, tylko pokaż podsumowanie}';
    protected $description = 'Wysyła do administratora dzienne zestawienie zaległości płatności dla grup mających zajęcia dzisiaj';

    public function handle(): int
    {
        $today = Carbon::now('Europe/Warsaw');
        $dateString = $today->toDateString();

        // Idempotencja: jeden digest na dzień
        $existing = AdminPaymentDigestLog::where('date', $dateString)->first();
        if ($existing && $existing->status === 'sent') {
            $this->info('Digest dla dzisiejszej daty już został wysłany.');
            return 0;
        }

        $weekdayNames = [
            0 => 'Niedziela',
            1 => 'Poniedziałek',
            2 => 'Wtorek',
            3 => 'Środa',
            4 => 'Czwartek',
            5 => 'Piątek',
            6 => 'Sobota',
        ];
        $weekday = $weekdayNames[$today->dayOfWeek] ?? (string) $today->dayOfWeek;

        // Znajdź grupy z dzisiejszego dnia wg nazwy (jak w SendPaymentReminders)
        $todayGroups = Group::where('name', 'like', "$weekday%")
            ->orWhere('name', 'like', "%$weekday%")
            ->get();

        if ($todayGroups->isEmpty()) {
            $this->warn("Brak grup dla dnia: $weekday");
        }

        $groupsDigest = [];
        $totalUsers = 0;
        $totalUnpaidCount = 0;
        $totalAmount = 0.0;

        foreach ($todayGroups as $group) {
            $users = User::where('group_id', $group->id)
                ->where('is_active', true)
                ->whereNot('role', 'admin')
                ->get();

            $groupUsers = [];
            foreach ($users as $user) {
                $unpaid = $this->getUnpaidPayments($user);
                if ($unpaid->isEmpty()) {
                    continue;
                }
                $months = $unpaid->map(function ($p) {
                    return Carbon::createFromFormat('Y-m', $p->month)->translatedFormat('F Y');
                })->toArray();

                $amount = (float) $unpaid->sum('amount');
                $groupUsers[] = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'months' => $months,
                    'amount' => $amount,
                ];

                $totalUsers++;
                $totalUnpaidCount += $unpaid->count();
                $totalAmount += $amount;
            }

            $groupsDigest[] = [
                'name' => $group->name,
                'users_count' => count($groupUsers),
                'users' => $groupUsers,
            ];
        }

        $digest = [
            'day_name' => $weekday,
            'date' => $dateString,
            'stats' => [
                'groups_count' => $todayGroups->count(),
                'users_count' => $totalUsers,
                'unpaid_count' => $totalUnpaidCount,
                'total_amount' => $totalAmount,
            ],
            'groups' => $groupsDigest,
        ];

        $subject = "Dzienne zestawienie zaległości: $weekday ($dateString)";

        $dryRun = (bool) $this->option('dry-run');
        if ($dryRun) {
            $this->info('[DRY-RUN] Podsumowanie:');
            $this->line(json_encode($digest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            try {
                $recipients = $this->getRecipients();
                if (empty($recipients)) {
                    $this->error('Brak skonfigurowanych odbiorców (ADMIN_PAYMENT_DIGEST_RECIPIENTS).');
                    return 1;
                }
                Mail::to($recipients)->send(new AdminDailyPaymentDigestMail($digest, $subject));
                // zapis logu tylko przy realnej wysyłce
                $this->storeLog($dateString, $weekday, 'sent', $todayGroups->count(), $totalUsers, $totalUnpaidCount, $totalAmount);
            } catch (\Throwable $e) {
                Log::error('Błąd wysyłki digestu do administratora', ['error' => $e->getMessage()]);
                $this->error('Błąd wysyłki digestu: ' . $e->getMessage());
                $this->storeLog($dateString, $weekday, 'failed', $todayGroups->count(), $totalUsers, $totalUnpaidCount, $totalAmount);
                return 1;
            }
        }
        $this->info('Wysłano dzienny digest do administratora.');
        return 0;
    }

    private function getRecipients(): array
    {
        $raw = (string) config('app.admin_payment_digest_recipients', env('ADMIN_PAYMENT_DIGEST_RECIPIENTS', ''));
        if (!$raw) {
            // Fallback: wszyscy administratorzy z bazy
            return \App\Models\User::where('role', 'admin')
                ->where('is_active', true)
                ->pluck('email')
                ->filter()
                ->values()
                ->all();
        }
        return collect(explode(',', $raw))
            ->map(fn ($e) => trim($e))
            ->filter()
            ->values()
            ->all();
    }

    private function getUnpaidPayments(User $user)
    {
        $currentMonth = Carbon::now()->format('Y-m');
        return Payment::where('user_id', $user->id)
            ->where('paid', false)
            ->where('month', '<=', $currentMonth)
            ->orderBy('month', 'asc')
            ->get();
    }

    private function storeLog(string $date, string $weekday, string $status, int $groupsCount, int $usersCount, int $unpaidCount, float $totalAmount): void
    {
        AdminPaymentDigestLog::updateOrCreate(
            ['date' => $date],
            [
                'weekday' => $weekday,
                'sent_at' => now(),
                'groups_count' => $groupsCount,
                'users_count' => $usersCount,
                'unpaid_count' => $unpaidCount,
                'total_amount' => $totalAmount,
                'status' => $status,
            ]
        );
    }
}


