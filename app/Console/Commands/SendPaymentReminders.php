<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Payment;
use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendPaymentReminders extends Command
{
    protected $signature = 'payments:send-reminders {--dry-run : Tylko pokaż co zostanie wysłane, nie wysyłaj}';
    protected $description = 'Wysyła przypomnienia o płatnościach do użytkowników z zaległościami';

    public function handle()
    {
        $this->info('Rozpoczynam wysyłanie przypomnień o płatnościach...');
        
        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('TRYB TESTOWY - nie wysyłam emaili');
        }

        $today = Carbon::now();
        $currentDayOfWeek = $today->dayOfWeek; // 1 = poniedziałek, 2 = wtorek, itd.
        
        // Mapowanie dni tygodnia na polskie nazwy
        $dayNames = [
            1 => 'Poniedziałek',
            2 => 'Wtorek', 
            3 => 'Środa',
            4 => 'Czwartek',
            5 => 'Piątek',
            6 => 'Sobota',
            7 => 'Niedziela'
        ];
        
        $currentDayName = $dayNames[$currentDayOfWeek];
        
        $this->info("Dzisiaj jest: {$currentDayName} ({$currentDayOfWeek})");
        
        // Znajdź grupy, które mają zajęcia dzisiaj
        $todayGroups = Group::where('name', 'like', "{$currentDayName}%")->get();
        
        if ($todayGroups->isEmpty()) {
            $this->warn("Brak grup z zajęciami dzisiaj ({$currentDayName})");
            return 0;
        }
        
        $this->info("Znaleziono grupy na dzisiaj: " . $todayGroups->pluck('name')->implode(', '));
        
        $totalReminders = 0;
        $totalUsers = 0;
        
        foreach ($todayGroups as $group) {
            $this->info("\nPrzetwarzam grupę: {$group->name}");
            
            // Pobierz użytkowników z tej grupy
            $users = User::where('group_id', $group->id)
                ->where('is_active', true)
                ->whereNot('role', 'admin')
                ->get();
            
            if ($users->isEmpty()) {
                $this->warn("  Brak aktywnych użytkowników w grupie");
                continue;
            }
            
            $this->info("  Użytkownicy w grupie: {$users->count()}");
            
            foreach ($users as $user) {
                $totalUsers++;
                
                // Sprawdź zaległości w płatnościach
                $unpaidPayments = $this->getUnpaidPayments($user);
                
                if ($unpaidPayments->isEmpty()) {
                    $this->line("  ✓ {$user->name} - wszystkie płatności uregulowane");
                    continue;
                }
                
                $this->warn("  ⚠ {$user->name} - ma {$unpaidPayments->count()} nieopłaconych płatności");
                
                // Przygotuj treść przypomnienia
                $reminderData = $this->prepareReminderData($user, $unpaidPayments, $group);
                
                if ($dryRun) {
                    $this->line("    [DRY RUN] Wysłano by przypomnienie do: {$user->email}");
                    $this->line("    [DRY RUN] Temat: {$reminderData['subject']}");
                    $this->line("    [DRY RUN] Treść: " . substr($reminderData['content'], 0, 100) . "...");
                } else {
                    try {
                        // Wyślij email
                        Mail::to($user->email)->send(
                            new \App\Mail\PaymentReminderMail($user, $reminderData['subject'], $reminderData['content'])
                        );
                        
                        $this->info("    ✓ Wysłano przypomnienie do: {$user->email}");
                        
                        // Zaloguj wysłanie
                        Log::info("Wysłano przypomnienie o płatności", [
                            'user_id' => $user->id,
                            'user_email' => $user->email,
                            'group' => $group->name,
                            'unpaid_count' => $unpaidPayments->count(),
                            'total_amount' => $unpaidPayments->sum('amount')
                        ]);
                        
                        $totalReminders++;
                        
                    } catch (\Exception $e) {
                        $this->error("    ✗ Błąd wysyłania do {$user->email}: " . $e->getMessage());
                        Log::error("Błąd wysyłania przypomnienia o płatności", [
                            'user_id' => $user->id,
                            'user_email' => $user->email,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }
        
        $this->newLine();
        $this->info("=== PODSUMOWANIE ===");
        $this->info("Przetworzono użytkowników: {$totalUsers}");
        $this->info("Wysłano przypomnień: {$totalReminders}");
        
        if ($dryRun) {
            $this->warn("TRYB TESTOWY - żadne emaile nie zostały wysłane");
        }
        
        return 0;
    }
    
    /**
     * Pobiera nieopłacone płatności użytkownika
     */
    private function getUnpaidPayments(User $user): \Illuminate\Database\Eloquent\Collection
    {
        $currentMonth = Carbon::now()->format('Y-m');
        
        return Payment::where('user_id', $user->id)
            ->where('paid', false)
            ->where('month', '<=', $currentMonth) // Płatności do bieżącego miesiąca włącznie
            ->orderBy('month', 'asc')
            ->get();
    }
    
    /**
     * Przygotowuje dane do przypomnienia
     */
    private function prepareReminderData(User $user, $unpaidPayments, Group $group): array
    {
        $totalAmount = $unpaidPayments->sum('amount');
        $unpaidCount = $unpaidPayments->count();
        
        // Określ typ zaległości
        $currentMonth = Carbon::now()->format('Y-m');
        $currentMonthPayment = $unpaidPayments->where('month', $currentMonth)->first();
        
        if ($currentMonthPayment) {
            $reminderType = 'bieżący';
            $subject = "Przypomnienie o płatności za {$currentMonth} - Grupa {$group->name}";
        } else {
            $reminderType = 'zaległy';
            $subject = "PILNE: Zaległości w płatnościach - Grupa {$group->name}";
        }
        
        // Przygotuj listę zaległych miesięcy
        $monthsList = $unpaidPayments->map(function ($payment) {
            $date = Carbon::createFromFormat('Y-m', $payment->month);
            return $date->translatedFormat('F Y');
        })->implode(', ');
        
        // Treść wiadomości
        $content = $this->generateReminderContent($user, $group, $unpaidPayments, $totalAmount, $monthsList, $reminderType);
        
        return [
            'subject' => $subject,
            'content' => $content,
            'type' => $reminderType
        ];
    }
    
    /**
     * Generuje treść przypomnienia
     */
    private function generateReminderContent(User $user, Group $group, $unpaidPayments, $totalAmount, $monthsList, $reminderType): string
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $currentMonthName = Carbon::now()->translatedFormat('F Y');
        
        $content = "<h2>Cześć {$user->name}!</h2>";
        
        if ($reminderType === 'bieżący') {
            $content .= "<p>Przypominamy o płatności za <strong>{$currentMonthName}</strong> w grupie <strong>{$group->name}</strong>.</p>";
        } else {
            $content .= "<p><strong>PILNE:</strong> Masz zaległości w płatnościach za następujące miesiące:</p>";
            $content .= "<ul><li>{$monthsList}</li></ul>";
        }
        
        $content .= "<div class='payment-summary'>";
        $content .= "<h3>Podsumowanie zaległości:</h3>";
        $content .= "<ul>";
        $content .= "<li>Liczba nieopłaconych miesięcy: <strong>{$unpaidPayments->count()}</strong></li>";
        $content .= "<li>Łączna kwota do zapłaty: <strong>{$totalAmount} zł</strong></li>";
        $content .= "</ul>";
        $content .= "</div>";
        
        $content .= "<div class='payment-details'>";
        $content .= "<h3>Szczegóły zaległości:</h3>";
        $content .= "<table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>";
        $content .= "<thead><tr style='background-color: #f3f4f6;'>";
        $content .= "<th style='padding: 10px; border: 1px solid #d1d5db; text-align: left;'>Miesiąc</th>";
        $content .= "<th style='padding: 10px; border: 1px solid #d1d5db; text-align: right;'>Kwota</th>";
        $content .= "</tr></thead><tbody>";
        
        foreach ($unpaidPayments as $payment) {
            $monthName = Carbon::createFromFormat('Y-m', $payment->month)->translatedFormat('F Y');
            $content .= "<tr>";
            $content .= "<td style='padding: 10px; border: 1px solid #d1d5db;'>{$monthName}</td>";
            $content .= "<td style='padding: 10px; border: 1px solid #d1d5db; text-align: right;'>{$payment->amount} zł</td>";
            $content .= "</tr>";
        }
        
        $content .= "</tbody></table>";
        $content .= "</div>";
        
        $content .= "<div class='action-required'>";
        $content .= "<h3>Co dalej?</h3>";
        $content .= "<p>Prosimy o uregulowanie zaległości w najbliższym możliwym terminie.</p>";
        
        if ($reminderType === 'zaległy') {
            $content .= "<p><strong>Uwaga:</strong> Długotrwałe zaległości mogą skutkować zawieszeniem uczestnictwa w zajęciach.</p>";
        }
        
        $content .= "</div>";
        
        $content .= "<div class='contact-info'>";
        $content .= "<p>Jeśli masz pytania lub chcesz ustalić plan spłaty, skontaktuj się z nami:</p>";
        $content .= "<ul>";
        $content .= "<li>Email: " . config('app.payment_reminder_email') . "</li>";
        $content .= "<li>Telefon: " . config('app.payment_reminder_phone') . "</li>";
        $content .= "</ul>";
        $content .= "</div>";
        
        $content .= "<p>Dziękujemy za zrozumienie!</p>";
        $content .= "<p><em>Zespół " . config('app.payment_reminder_company_name') . "</em></p>";
        
        return $content;
    }
}
