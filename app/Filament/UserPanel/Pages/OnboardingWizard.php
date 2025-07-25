<?php

namespace App\Filament\UserPanel\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Html;
use Filament\Forms\Components\Placeholder;

class OnboardingWizard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $slug = 'onboarding';
    protected static ?string $title = 'Onboarding';
    protected static ?string $navigationLabel = null;
    protected static string $view = 'filament.user-panel.pages.onboarding-wizard';

    public $address_street = '';
    public $address_postal_code = '';
    public $address_city = '';
    public $rodo_accept = false;
    public $terms_accept = false;

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $address = $user->addresses()->first();

        $this->form->fill([
            'address_street' => $address?->street ?? '',
            'address_postal_code' => $address?->postal_code ?? '',
            'address_city' => $address?->city ?? '',
            'rodo_accept' => !is_null($user->rodo_accepted_at),
            'terms_accept' => !is_null($user->terms_accepted_at),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Wizard::make([
                Forms\Components\Wizard\Step::make('Adres')
                    ->schema([
                        Forms\Components\TextInput::make('address_street')
                            ->label('Ulica i numer')
                            ->required(),
                        Forms\Components\TextInput::make('address_postal_code')
                            ->label('Kod pocztowy')
                            ->required(),
                        Forms\Components\TextInput::make('address_city')
                            ->label('Miasto')
                            ->required(),
                    ]),
                Forms\Components\Wizard\Step::make('RODO')
                    ->schema([
                        Forms\Components\Checkbox::make('rodo_accept')
                            ->label('Wyrażam zgodę na przetwarzanie moich danych osobowych zgodnie z RODO')
                            ->accepted()
                            ->required(),
                        Forms\Components\Placeholder::make('rodo_info')
                            ->label('Informacja RODO')
                            ->content('Twoje dane będą przetwarzane zgodnie z obowiązującymi przepisami RODO. Administratorem danych jest właściciel serwisu.'),
                    ]),
                Forms\Components\Wizard\Step::make('Regulamin')
                    ->schema([
                        Forms\Components\Checkbox::make('terms_accept')
                            ->label('Zapoznałem się i akceptuję regulamin korzystania z panelu')
                            ->accepted()
                            ->required(),
                        Forms\Components\Placeholder::make('terms_info')
                            ->label('Regulamin')
                            ->content('Regulamin korzystania z panelu dostępny jest na stronie głównej. Akceptacja regulaminu jest wymagana do korzystania z serwisu.'),
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('submit')
                                ->label('Zapisz i przejdź do panelu')
                                ->submit('submit')
                                ->color('primary')
                                ->extraAttributes(['class' => 'ml-auto']),
                        ])->alignEnd(),
                    ]),
            ])
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();

        // RĘCZNA WALIDACJA
        if (empty($data['rodo_accept'])) {
            $this->addError('rodo_accept', 'Musisz zaakceptować zgodę RODO.');
            return;
        }
        if (empty($data['terms_accept'])) {
            $this->addError('terms_accept', 'Musisz zaakceptować regulamin.');
            return;
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            Notification::make()->danger()->title('Błąd')->body('Nie znaleziono użytkownika.')->send();
            return;
        }
        // Zapisz adres
        Address::updateOrCreate(
            ['user_id' => $user->id],
            [
                'street' => $data['address_street'],
                'postal_code' => $data['address_postal_code'],
                'city' => $data['address_city'],
            ]
        );
        // Zapisz RODO i regulamin
        $user->rodo_accepted_at = now();
        $user->terms_accepted_at = now();
        $user->save();
        Notification::make()->success()->title('Dziękujemy!')->body('Onboarding zakończony.')->send();
        return redirect()->route('filament.user.pages.dashboard');
    }
} 