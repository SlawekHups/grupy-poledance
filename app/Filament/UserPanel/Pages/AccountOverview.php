<?php

namespace App\Filament\UserPanel\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use App\Models\Group;
use App\Models\Attendance;
use App\Models\UserMailMessage;

class AccountOverview extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Konto';
    protected static ?string $title = 'Konto użytkownika';
    protected static ?string $slug = 'account';
    protected static ?string $navigationGroup = 'Moje konto';

    protected static string $view = 'filament.user.account-overview';

    public int $unpaidCount = 0;
    public float $totalDue = 0.0;
    public ?string $paymentLink = null;
    public ?string $groupName = null;
    public $recentPayments = [];
    public int $presentCount = 0;
    public int $absentCount = 0;
    public int $paymentsCount = 0;
    public float $paymentsSum = 0.0;

    // Wiadomości
    public int $messagesCount = 0;
    public int $messagesInCount = 0;
    public int $messagesOutCount = 0;
    public array $recentMessages = [];

    public function mount(): void
    {
        $user = Auth::user();

        $unpaid = Payment::where('user_id', $user->id)
            ->where('paid', false)
            ->orderBy('month', 'asc')
            ->get();

        $this->unpaidCount = $unpaid->count();
        $this->totalDue = (float) $unpaid->sum('amount');
        $this->paymentLink = $this->getPaymentLink();
        $this->groupName = optional($user->group)->name;

        // Ostatnie 3 płatności (miesiące)
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

        // Liczniki obecności
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

    protected function getHeaderActions(): array
    {
        $user = Auth::user();

        return [
            Action::make('edit')
                ->label('Edytuj profil')
                ->icon('heroicon-o-pencil-square')
                ->url(fn () => route('filament.user.resources.users.edit', ['record' => $user->id]))
                ->color('primary'),
            Action::make('profile')
                ->label('Zmień hasło')
                ->icon('heroicon-o-key')
                ->url(fn () => route('filament.user.auth.profile')),
            Action::make('export')
                ->label('Pobierz moje dane (CSV)')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn () => route('user.export-my-csv'))
                ->openUrlInNewTab()
                ->color('gray'),
        ];
    }

    private function getPaymentLink(): ?string
    {
        $user = Auth::user();
        $currentMonth = now()->format('Y-m');

        $payment = Payment::where('user_id', $user->id)
            ->where('month', $currentMonth)
            ->whereNotNull('payment_link')
            ->first();

        if ($payment && $payment->payment_link) {
            return $payment->payment_link;
        }

        $unpaidPayment = Payment::where('user_id', $user->id)
            ->where('paid', false)
            ->whereNotNull('payment_link')
            ->orderBy('month', 'desc')
            ->first();

        return $unpaidPayment?->payment_link;
    }
}
