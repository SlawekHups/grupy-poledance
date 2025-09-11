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
use Filament\Forms\Components\ViewField;

class OnboardingWizard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $slug = 'onboarding';
    protected static ?string $title = 'Rejestracja';
    protected static ?string $navigationLabel = null;
    protected static string $view = 'filament.user-panel.pages.onboarding-wizard';

    public $phone = '';
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
            'phone' => $user->phone ?? '',
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
                        // ViewField welcome usunięty, komunikat jest tylko w widoku Blade
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon')
                            ->tel()
                            ->required()
                            ->minLength(9)
                            ->maxLength(15)
                            ->validationMessages([
                                'required' => 'Numer telefonu jest wymagany.',
                                'minLength' => 'Numer telefonu musi mieć minimum 9 cyfr.',
                                'maxLength' => 'Numer telefonu może mieć maksymalnie 15 cyfr.',
                            ])
                            ->dehydrateStateUsing(function ($state) {
                                if (!$state) return null;
                                $number = preg_replace('/\\D/', '', $state);
                                if (strlen($number) === 9) {
                                    return '+48' . $number;
                                }
                                return $state;
                            }),
                        Forms\Components\TextInput::make('address_street')
                            ->label('Ulica i numer')
                            ->required()
                            ->validationMessages([
                                'required' => 'Ulica i numer są wymagane.',
                            ]),
                        Forms\Components\TextInput::make('address_postal_code')
                            ->label('Kod pocztowy')
                            ->required()
                            ->validationMessages([
                                'required' => 'Kod pocztowy jest wymagany.',
                            ]),
                        Forms\Components\TextInput::make('address_city')
                            ->label('Miasto')
                            ->required()
                            ->validationMessages([
                                'required' => 'Miasto jest wymagane.',
                            ]),
                    ]),
                Forms\Components\Wizard\Step::make('RODO')
                    ->schema([
                        Forms\Components\Checkbox::make('rodo_accept')
                            ->label('Wyrażam zgodę na przetwarzanie moich danych osobowych zgodnie z RODO')
                            ->accepted()
                            ->required()
                            ->validationMessages([
                                'accepted' => 'Musisz zaakceptować zgodę RODO.',
                                'required' => 'Zgoda RODO jest wymagana.',
                            ]),
                        Forms\Components\Placeholder::make('rodo_info')
                            ->label('Informacja RODO')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="space-y-4">
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <h4 class="font-semibold text-blue-900 mb-2">📋 Administrator danych</h4>
                                        <p class="text-blue-800 text-sm">Administratorem Twoich danych osobowych jest właściciel serwisu Grupy Poledance.</p>
                                    </div>
                                    
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <h4 class="font-semibold text-green-900 mb-2">🎯 Cel przetwarzania</h4>
                                        <p class="text-green-800 text-sm">Twoje dane są przetwarzane w celu:</p>
                                        <ul class="text-green-800 text-sm mt-2 ml-4 list-disc space-y-1">
                                            <li>Zarządzania zajęciami i grupami</li>
                                            <li>Kontaktowania się w sprawach organizacyjnych</li>
                                            <li>Wysyłania przypomnień o płatnościach</li>
                                            <li>Prowadzenia ewidencji obecności</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <h4 class="font-semibold text-yellow-900 mb-2">⚖️ Twoje prawa</h4>
                                        <p class="text-yellow-800 text-sm">Masz prawo do:</p>
                                        <ul class="text-yellow-800 text-sm mt-2 ml-4 list-disc space-y-1">
                                            <li>Dostępu do swoich danych</li>
                                            <li>Poprawienia nieprawidłowych danych</li>
                                            <li>Usunięcia danych (prawo do zapomnienia)</li>
                                            <li>Przeniesienia danych</li>
                                            <li>Wycofania zgody w dowolnym momencie</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 mb-2">📞 Kontakt</h4>
                                        <p class="text-gray-800 text-sm">
                                            W sprawach związanych z przetwarzaniem danych skontaktuj się:<br>
                                            📧 <a href="mailto:' . config('app.payment_reminder_email') . '" class="text-blue-600 hover:text-blue-500">' . config('app.payment_reminder_email') . '</a><br>
                                            ☎️ ' . config('app.payment_reminder_phone') . '
                                        </p>
                                    </div>
                                </div>
                            ')),
                    ]),
                Forms\Components\Wizard\Step::make('Regulamin')
                    ->schema([
                        Forms\Components\Checkbox::make('terms_accept')
                            ->label('Zapoznałem się i akceptuję regulamin korzystania z panelu')
                            ->accepted()
                            ->required()
                            ->validationMessages([
                                'accepted' => 'Musisz zaakceptować regulamin.',
                                'required' => 'Akceptacja regulaminu jest wymagana.',
                            ]),
                        Forms\Components\Placeholder::make('terms_info')
                            ->label('Regulamin')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="space-y-4">
                                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                                        <h4 class="font-semibold text-orange-900 mb-2">📋 Regulamin korzystania z panelu</h4>
                                        <p class="text-orange-800 text-sm">Akceptując regulamin, zobowiązujesz się do przestrzegania poniższych zasad:</p>
                                    </div>
                                    
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <h4 class="font-semibold text-blue-900 mb-2">🔐 Bezpieczeństwo konta</h4>
                                        <ul class="text-blue-800 text-sm mt-2 ml-4 list-disc space-y-1">
                                            <li>Nie udostępniaj swojego hasła innym osobom</li>
                                            <li>Wyloguj się z panelu po zakończeniu pracy</li>
                                            <li>Natychmiast zgłoś podejrzaną aktywność</li>
                                            <li>Regularnie aktualizuj swoje dane kontaktowe</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <h4 class="font-semibold text-green-900 mb-2">💳 Płatności</h4>
                                        <ul class="text-green-800 text-sm mt-2 ml-4 list-disc space-y-1">
                                            <li>Płatności należy regulować zgodnie z harmonogramem</li>
                                            <li>Zaległości w płatnościach mogą skutkować zawieszeniem dostępu</li>
                                            <li>Wszelkie zmiany w płatnościach należy zgłaszać z wyprzedzeniem</li>
                                            <li>System automatycznie wysyła przypomnienia o płatnościach</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                        <h4 class="font-semibold text-purple-900 mb-2">📅 Obecności i zajęcia</h4>
                                        <ul class="text-purple-800 text-sm mt-2 ml-4 list-disc space-y-1">
                                            <li>Obecność na zajęciach jest rejestrowana automatycznie</li>
                                            <li>Nieobecności należy zgłaszać z wyprzedzeniem</li>
                                            <li>Regularne uczestnictwo w zajęciach jest wymagane</li>
                                            <li>Zmiana grupy wymaga zgody administratora</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                        <h4 class="font-semibold text-red-900 mb-2">⚠️ Zakazane zachowania</h4>
                                        <ul class="text-red-800 text-sm mt-2 ml-4 list-disc space-y-1">
                                            <li>Używanie panelu do celów niezwiązanych z zajęciami</li>
                                            <li>Próby naruszenia bezpieczeństwa systemu</li>
                                            <li>Udostępnianie danych innych uczestników</li>
                                            <li>Nieprzestrzeganie zasad kultury i szacunku</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 mb-2">📞 Wsparcie i kontakt</h4>
                                        <p class="text-gray-800 text-sm">
                                            W przypadku pytań lub problemów skontaktuj się:<br>
                                            📧 <a href="mailto:' . config('app.payment_reminder_email') . '" class="text-blue-600 hover:text-blue-500">' . config('app.payment_reminder_email') . '</a><br>
                                            ☎️ ' . config('app.payment_reminder_phone') . '<br>
                                            🌐 <a href="https://' . config('app.payment_reminder_website') . '" class="text-blue-600 hover:text-blue-500" target="_blank">' . config('app.payment_reminder_website') . '</a>
                                        </p>
                                    </div>
                                    
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <h4 class="font-semibold text-yellow-900 mb-2">📝 Ważne informacje</h4>
                                        <ul class="text-yellow-800 text-sm mt-2 ml-4 list-disc space-y-1">
                                            <li>Regulamin może być zmieniany z zachowaniem 7-dniowego okresu wypowiedzenia</li>
                                            <li>O zmianach będziesz informowany przez email</li>
                                            <li>Kontynuowanie korzystania z panelu oznacza akceptację zmian</li>
                                            <li>W przypadku naruszenia regulaminu konto może zostać zawieszone</li>
                                        </ul>
                                    </div>
                                </div>
                            ')),
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

        // Walidacja jest teraz obsługiwana przez validationMessages() w polach formularza

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            Notification::make()->danger()->title('Błąd')->body('Nie znaleziono użytkownika.')->send();
            return;
        }
        
        // Zapisz telefon
        $user->phone = $data['phone'];
        
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

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if (!$user) return false;
        // Ukryj onboarding jeśli user ma adres, rodo i regulamin
        return (
            $user->addresses()->count() === 0 ||
            is_null($user->rodo_accepted_at) ||
            is_null($user->terms_accepted_at)
        );
    }
} 