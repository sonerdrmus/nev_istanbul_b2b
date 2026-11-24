<?php

namespace App\Filament\Pages;

use App\Models\MailSetting;
use App\Services\MailConfigApplicator;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ManageMailSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'E-posta ayarları';

    protected static ?string $title = 'E-posta / SMTP ayarları';

    protected static ?string $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 90;

    protected static string $view = 'filament.pages.manage-mail-settings';

    public ?array $data = [];

    public string $test_email = '';

    public static function getSlug(): string
    {
        return 'mail-settings';
    }

    public function mount(): void
    {
        $settings = MailSetting::current();

        $this->form->fill([
            'enabled' => $settings->enabled,
            'mailer' => $settings->mailer ?: 'smtp',
            'host' => $settings->host,
            'port' => $settings->port ?: 587,
            'scheme' => $settings->scheme ?: 'tls',
            'username' => $settings->username,
            'password' => '',
            'from_address' => $settings->from_address,
            'from_name' => $settings->from_name ?: config('app.name'),
        ]);

        $this->test_email = (string) (auth()->user()?->email ?? '');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Gönderim')
                    ->description('Burada kayıtlı SMTP bilgileri .env yerine kullanılır. Kapalıysa uygulama .env MAIL_* değerlerine düşer.')
                    ->schema([
                        Forms\Components\Toggle::make('enabled')
                            ->label('Admin paneli mail ayarlarını kullan')
                            ->helperText('Açıkken kayıt, onay ve iletişim mailleri bu ayarlarla gider.')
                            ->live(),
                        Forms\Components\Select::make('mailer')
                            ->label('Mailer')
                            ->options([
                                'smtp' => 'SMTP',
                                'log' => 'Log (yalnızca test — gerçek mail gitmez)',
                            ])
                            ->required()
                            ->native(false),
                    ]),

                Forms\Components\Section::make('SMTP sunucu')
                    ->visible(fn (Forms\Get $get): bool => ($get('mailer') ?: 'smtp') === 'smtp')
                    ->schema([
                        Forms\Components\TextInput::make('host')
                            ->label('Host')
                            ->placeholder('smtp.ornek.com')
                            ->maxLength(255)
                            ->required(fn (Forms\Get $get): bool => (bool) $get('enabled') && ($get('mailer') ?: 'smtp') === 'smtp'),
                        Forms\Components\TextInput::make('port')
                            ->label('Port')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(65535)
                            ->default(587)
                            ->required(fn (Forms\Get $get): bool => (bool) $get('enabled') && ($get('mailer') ?: 'smtp') === 'smtp'),
                        Forms\Components\Select::make('scheme')
                            ->label('Şifreleme')
                            ->options([
                                'tls' => 'TLS (önerilen, port 587)',
                                'ssl' => 'SSL (port 465)',
                                '' => 'Yok',
                            ])
                            ->native(false),
                        Forms\Components\TextInput::make('username')
                            ->label('Kullanıcı adı')
                            ->maxLength(255)
                            ->autocomplete(false),
                        Forms\Components\TextInput::make('password')
                            ->label('Şifre')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->helperText('Boş bırakırsanız kayıtlı şifre değişmez.'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Gönderen')
                    ->schema([
                        Forms\Components\TextInput::make('from_address')
                            ->label('Gönderen e-posta')
                            ->email()
                            ->maxLength(255)
                            ->required(fn (Forms\Get $get): bool => (bool) $get('enabled')),
                        Forms\Components\TextInput::make('from_name')
                            ->label('Gönderen adı')
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(MailConfigApplicator $applicator): void
    {
        $state = $this->form->getState();
        $settings = MailSetting::current();

        $payload = [
            'enabled' => (bool) ($state['enabled'] ?? false),
            'mailer' => (string) ($state['mailer'] ?? 'smtp'),
            'host' => $state['host'] ?? null,
            'port' => isset($state['port']) ? (int) $state['port'] : null,
            'scheme' => ($state['scheme'] ?? '') === '' ? null : ($state['scheme'] ?? null),
            'username' => $state['username'] ?? null,
            'from_address' => $state['from_address'] ?? null,
            'from_name' => $state['from_name'] ?? null,
        ];

        if (array_key_exists('password', $state) && filled($state['password'])) {
            $payload['password'] = $state['password'];
        }

        $settings->update($payload);
        $applicator->apply($settings->fresh());

        Notification::make()
            ->title('E-posta ayarları kaydedildi')
            ->success()
            ->send();
    }

    public function sendTest(MailConfigApplicator $applicator): void
    {
        $email = trim($this->test_email);
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Notification::make()
                ->title('Geçerli bir test e-postası girin')
                ->danger()
                ->send();

            return;
        }

        $this->save($applicator);

        $settings = MailSetting::current()->fresh();
        if (! $settings->enabled || ! $settings->isConfigured()) {
            Notification::make()
                ->title('Önce ayarları etkinleştirip kaydedin')
                ->warning()
                ->send();

            return;
        }

        try {
            Mail::raw(
                "Nevistanbul test maili.\nZaman: ".now()->toDateTimeString()."\nBu mesaj admin panelinden gönderildi.",
                function ($message) use ($email, $settings): void {
                    $message->to($email)
                        ->subject('Test maili – '.($settings->from_name ?: config('app.name')));
                }
            );

            Notification::make()
                ->title('Test maili gönderildi')
                ->body($email)
                ->success()
                ->send();
        } catch (Throwable $e) {
            Notification::make()
                ->title('Test maili gönderilemedi')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
