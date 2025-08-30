<?php

namespace App\Filament\UserPanel\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use App\Models\Attendance;
use App\Models\UserMailMessage;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Panel główny';
    protected static ?string $title = 'Panel użytkownika';
    protected static ?string $slug = 'dashboard';
    protected static bool $shouldRegisterNavigation = true;
    
    public int $unpaidCount = 0;
    public float $totalDue = 0.0;
    public ?string $paymentLink = null;
    public ?string $groupName = null;
    public array $recentPayments = [];
    public int $presentCount = 0;
    public int $absentCount = 0;
    public int $paymentsCount = 0;
    public float $paymentsSum = 0.0;
    public int $messagesCount = 0;
    public int $messagesInCount = 0;
    public int $messagesOutCount = 0;
    public array $recentMessages = [];

    protected static string $view = 'filament.user.dashboard';

    public function mount()
    {
        $user = auth()->user();
        if (
            $user->addresses()->count() === 0 ||
            is_null($user->rodo_accepted_at) ||
            is_null($user->terms_accepted_at)
        ) {
            return redirect()->route('filament.user.pages.onboarding');
        }

        // Dane z konta na dashboard
        $unpaid = Payment::where('user_id', $user->id)
            ->where('paid', false)
            ->get();
        $this->unpaidCount = $unpaid->count();
        $this->totalDue = (float) $unpaid->sum('amount');
        $this->paymentLink = $this->getPaymentLink($user->id);
        $this->groupName = optional($user->group)->name;

        // Ostatnie 3 płatności
        $this->recentPayments = Payment::where('user_id', $user->id)
            ->orderBy('month', 'desc')
            ->limit(3)
            ->get(['month', 'amount', 'paid', 'payment_link'])
            ->map(function ($p) {
                return [
                    'month' => $p->month,
                    'amount' => (float) $p->amount,
                    'paid' => (bool) $p->paid,
                    'payment_link' => $p->payment_link,
                ];
            })->toArray();

        $this->presentCount = Attendance::where('user_id', $user->id)->where('present', true)->count();
        $this->absentCount = Attendance::where('user_id', $user->id)->where('present', false)->count();

        // Płatności: liczba wszystkich i suma wszystkich
        $paymentsQuery = Payment::where('user_id', $user->id);
        $this->paymentsCount = (clone $paymentsQuery)->count();
        $this->paymentsSum = (float) (clone $paymentsQuery)->sum('amount');

        // Wiadomości: liczniki i ostatnie pozycje
        $messagesQuery = UserMailMessage::where('user_id', $user->id);
        $this->messagesCount = (clone $messagesQuery)->count();
        $this->messagesInCount = (clone $messagesQuery)->where('direction', 'in')->count();
        $this->messagesOutCount = (clone $messagesQuery)->where('direction', 'out')->count();
        $this->recentMessages = UserMailMessage::where('user_id', $user->id)
            ->orderBy('sent_at', 'desc')
            ->limit(3)
            ->get(['id', 'subject', 'direction', 'email', 'sent_at'])
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'subject' => $m->subject,
                    'direction' => $m->direction,
                    'email' => $m->email,
                    'sent_at' => $m->sent_at,
                ];
            })->toArray();
    }

    private function getPaymentLink(int $userId): ?string
    {
        $currentMonth = now()->format('Y-m');
        $payment = Payment::where('user_id', $userId)
            ->where('month', $currentMonth)
            ->whereNotNull('payment_link')
            ->first();

        if ($payment && $payment->payment_link) {
            return $payment->payment_link;
        }

        $unpaidPayment = Payment::where('user_id', $userId)
            ->where('paid', false)
            ->whereNotNull('payment_link')
            ->orderBy('month', 'desc')
            ->first();

        return $unpaidPayment?->payment_link;
    }

    // Akcje nagłówka przeniesione do widoku (ukrywanie na mobile)
} 