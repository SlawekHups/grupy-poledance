<?php

namespace App\Filament\UserPanel\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class PaymentsKpis extends Widget
{
    protected static string $view = 'filament.user.widgets.payments-kpis';

    protected int|string|array $columnSpan = 'full';

    public float $paymentsSum = 0.0;
    public int $paymentsCount = 0;
    public float $unpaidSum = 0.0;

    public function mount(): void
    {
        $userId = Auth::id();
        $query = Payment::where('user_id', $userId);
        $this->paymentsCount = (clone $query)->count();
        $this->paymentsSum = (float) (clone $query)->sum('amount');
        $this->unpaidSum = (float) (clone $query)->where('paid', false)->sum('amount');
    }
}
