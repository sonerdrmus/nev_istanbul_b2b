<?php

namespace App\Filament\Pages;

use App\Models\MediaStorageSetting;
use App\Models\StoreSetting;
use App\Services\MediaStorageConfigApplicator;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageMediaSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cloud';

    protected static ?string $navigationLabel = 'Medya / R2 / CDN';

    protected static ?string $title = 'Medya depolama (Cloudflare R2 / S3)';

    protected static ?string $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 91;

    protected static string $view = 'filament.pages.manage-media-settings';

    public ?array $data = [];

    public static function getSlug(): string
    {
        return 'media-settings';
    }

    public function mount(): void
    {
        $settings = MediaStorageSetting::current();
        $legacyCdn = StoreSetting::hasMediaCdnColumn()
            ? trim((string) (StoreSetting::current()->media_cdn_url ?? ''))
            : '';

        $this->form->fill([
            'enabled' => $settings->enabled,
            'provider' => $settings->provider ?: 'r2',
            'name' => $settings->name ?: 'nevistanbul',
            'location' => $settings->location ?: 'Eastern Europe (EEUR)',
            'bucket' => $settings->bucket ?: 'nevistanbul',
            'endpoint' => $settings->endpoint
                ?: 'https://2777cc3ede818bebf907d1a2fa84428e.r2.cloudflarestorage.com',
            'region' => $settings->region ?: 'auto',
            'access_key_id' => $settings->access_key_id,
            'secret_access_key' => '',
            'public_url' => $settings->public_url ?: $legacyCdn ?: (string) config('store.media_cdn_url', ''),
            'use_path_style_endpoint' => $settings->use_path_style_endpoint ?? true,
            'use_for_uploads' => $settings->use_for_uploads,
            'has_secret' => filled($settings->secret_access_key),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Genel')
                    ->description('Cloudflare R2 (S3 uyumlu) veya Amazon S3 bilgileri. Bu panel .env AWS_* değerlerinin yerine geçebilir.')
                    ->schema([
                        Forms\Components\Toggle::make('enabled')
                            ->label('Admin paneli medya ayarlarını kullan')
                            ->helperText('Açıkken aşağıdaki bilgiler runtime’da S3 diskine uygulanır. Kapalıysa .env AWS_* kullanılır.')
                            ->live(),
                        Forms\Components\Select::make('provider')
                            ->label('Sağlayıcı')
                            ->options([
                                'r2' => 'Cloudflare R2',
                                's3' => 'Amazon S3 / uyumlu',
                            ])
                            ->native(false)
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Name (bucket görünen ad)')
                            ->placeholder('nevistanbul')
                            ->maxLength(255)
                            ->helperText('Cloudflare panelindeki bucket Name alanı.'),
                        Forms\Components\TextInput::make('location')
                            ->label('Location')
                            ->placeholder('Eastern Europe (EEUR)')
                            ->maxLength(255)
                            ->helperText('Bilgi amaçlı; R2 region genelde auto bırakılır.'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('S3 API')
                    ->description('R2 → bucket → Settings / S3 API. Endpoint’e bucket adını eklemeyin (uygulama otomatik ayıklar).')
                    ->schema([
                        Forms\Components\TextInput::make('endpoint')
                            ->label('S3 API endpoint')
                            ->placeholder('https://….r2.cloudflarestorage.com')
                            ->maxLength(500)
                            ->required(fn (Forms\Get $get): bool => (bool) $get('enabled'))
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('bucket')
                            ->label('Bucket')
                            ->placeholder('nevistanbul')
                            ->maxLength(255)
                            ->required(fn (Forms\Get $get): bool => (bool) $get('enabled')),
                        Forms\Components\TextInput::make('region')
                            ->label('Region')
                            ->placeholder('auto')
                            ->maxLength(64)
                            ->helperText('Cloudflare R2 için genelde auto.'),
                        Forms\Components\Toggle::make('use_path_style_endpoint')
                            ->label('Path-style endpoint')
                            ->helperText('R2 için açık tutun (önerilen).')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('API anahtarları')
                    ->description('Cloudflare → R2 → Manage R2 API Tokens ile Access Key ID + Secret Access Key oluşturun. Cloudflare Global API Key buraya yazılmamalı.')
                    ->schema([
                        Forms\Components\TextInput::make('access_key_id')
                            ->label('Access Key ID')
                            ->maxLength(255)
                            ->autocomplete(false)
                            ->required(fn (Forms\Get $get): bool => (bool) $get('enabled'))
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('secret_access_key')
                            ->label('Secret Access Key')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->helperText(fn (Forms\Get $get): string => ($get('has_secret')
                                ? 'Kayıtlı gizli anahtar var. Boş bırakırsanız değişmez.'
                                : 'Henüz gizli anahtar kaydedilmedi.')),
                        Forms\Components\Hidden::make('has_secret'),
                    ]),

                Forms\Components\Section::make('Genel erişim / CDN')
                    ->description('Tarayıcıda görsellerin açılacağı genel URL (R2 custom domain veya r2.dev). Preconnect için de kullanılır.')
                    ->schema([
                        Forms\Components\TextInput::make('public_url')
                            ->label('Public / CDN URL')
                            ->placeholder('https://medya.nevistanbul.com.tr')
                            ->url()
                            ->maxLength(500)
                            ->helperText('S3 API adresini buraya yazmayın; yalnızca herkese açık medya kökü.')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('use_for_uploads')
                            ->label('Yüklemelerde bu depoyu kullan (public disk → R2/S3)')
                            ->helperText('Açıkken admin yüklemeleri ve MediaUrl bu S3/R2 hesabına gider. Kapalıysa dosyalar yerel public diskte kalır; yalnızca S3 config + CDN preconnect uygulanır.')
                            ->visible(fn (Forms\Get $get): bool => (bool) $get('enabled')),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(MediaStorageConfigApplicator $applicator): void
    {
        $state = $this->form->getState();
        $settings = MediaStorageSetting::current();

        $endpoint = trim((string) ($state['endpoint'] ?? ''));
        $bucket = trim((string) ($state['bucket'] ?? ''));
        $publicUrl = trim((string) ($state['public_url'] ?? ''));

        if ($publicUrl !== '') {
            $publicUrl = rtrim($publicUrl, '/');
            if (! preg_match('#^https?://#i', $publicUrl)) {
                $publicUrl = 'https://'.$publicUrl;
            }
        }

        if ($endpoint !== '') {
            $endpoint = rtrim($endpoint, '/');
            if (! preg_match('#^https?://#i', $endpoint)) {
                $endpoint = 'https://'.$endpoint;
            }
            // Panelden bucket’lı yapıştırılırsa ayıkla
            if ($bucket !== '' && str_ends_with(strtolower($endpoint), '/'.strtolower($bucket))) {
                $endpoint = substr($endpoint, 0, -strlen($bucket) - 1);
            }
        }

        $payload = [
            'enabled' => (bool) ($state['enabled'] ?? false),
            'provider' => (string) ($state['provider'] ?? 'r2'),
            'name' => trim((string) ($state['name'] ?? '')) ?: null,
            'location' => trim((string) ($state['location'] ?? '')) ?: null,
            'bucket' => $bucket !== '' ? $bucket : null,
            'endpoint' => $endpoint !== '' ? $endpoint : null,
            'region' => trim((string) ($state['region'] ?? 'auto')) ?: 'auto',
            'access_key_id' => trim((string) ($state['access_key_id'] ?? '')) ?: null,
            'public_url' => $publicUrl !== '' ? $publicUrl : null,
            'use_path_style_endpoint' => (bool) ($state['use_path_style_endpoint'] ?? true),
            'use_for_uploads' => (bool) ($state['use_for_uploads'] ?? false),
        ];

        if (array_key_exists('secret_access_key', $state) && filled($state['secret_access_key'])) {
            $payload['secret_access_key'] = $state['secret_access_key'];
        }

        $settings->update($payload);

        if (StoreSetting::hasMediaCdnColumn()) {
            StoreSetting::current()->update([
                'media_cdn_url' => $publicUrl !== '' ? $publicUrl : null,
            ]);
        }

        $applicator->apply($settings->fresh());

        $this->form->fill(array_merge($this->form->getState(), [
            'secret_access_key' => '',
            'has_secret' => filled($settings->fresh()->secret_access_key),
            'endpoint' => $settings->fresh()->endpoint,
            'public_url' => $settings->fresh()->public_url,
        ]));

        Notification::make()
            ->title('Medya depolama ayarları kaydedildi')
            ->success()
            ->send();
    }
}
