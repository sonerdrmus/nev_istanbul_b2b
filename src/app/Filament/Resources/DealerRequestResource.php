<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DealerRequestResource\Pages;
use App\Models\DealerRequest;
use App\Services\DealerRequestApprover;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class DealerRequestResource extends Resource
{
    protected static ?string $model = DealerRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'B2B Yönetimi';

    protected static ?string $modelLabel = 'Bayilik Talebi';

    protected static ?string $pluralModelLabel = 'Bayi Talepleri';

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = DealerRequest::where('status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Kişi / iletişim')
                ->schema([
                    Forms\Components\TextInput::make('first_name')->label('Ad')->disabled(),
                    Forms\Components\TextInput::make('last_name')->label('Soyad')->disabled(),
                    Forms\Components\TextInput::make('full_name')->label('Ad Soyad (birleşik)')->disabled(),
                    Forms\Components\TextInput::make('email')->label('E-posta')->disabled(),
                    Forms\Components\TextInput::make('phone')->label('Ana telefon')->disabled(),
                    Forms\Components\TextInput::make('mobile_phone')->label('Mobil')->disabled(),
                    Forms\Components\TextInput::make('tc_no')->label('T.C. (eski başvuru)')->disabled()->placeholder('—'),
                ])->columns(2),

            Forms\Components\Section::make('İş adresi')
                ->schema([
                    Forms\Components\TextInput::make('business_name')->label('İşletme adı')->disabled(),
                    Forms\Components\TextInput::make('address_line_1')->label('Adres satırı 1')->disabled(),
                    Forms\Components\TextInput::make('address_line_2')->label('Adres satırı 2')->disabled(),
                    Forms\Components\TextInput::make('city')->label('Şehir')->disabled(),
                    Forms\Components\TextInput::make('postcode')->label('Posta kodu')->disabled(),
                    Forms\Components\TextInput::make('country')->label('Ülke')->disabled(),
                    Forms\Components\Toggle::make('different_delivery_address')->label('Teslimat adresi farklı')->disabled()->columnSpanFull(),
                    Forms\Components\TextInput::make('delivery_address_line_1')
                        ->label('Teslimat adres satırı 1')
                        ->disabled()
                        ->visible(fn (DealerRequest $record): bool => (bool) $record->different_delivery_address),
                    Forms\Components\TextInput::make('delivery_address_line_2')
                        ->label('Teslimat adres satırı 2')
                        ->disabled()
                        ->visible(fn (DealerRequest $record): bool => (bool) $record->different_delivery_address),
                    Forms\Components\TextInput::make('delivery_city')
                        ->label('Teslimat şehir')
                        ->disabled()
                        ->visible(fn (DealerRequest $record): bool => (bool) $record->different_delivery_address),
                    Forms\Components\TextInput::make('delivery_postcode')
                        ->label('Teslimat posta kodu')
                        ->disabled()
                        ->visible(fn (DealerRequest $record): bool => (bool) $record->different_delivery_address),
                    Forms\Components\TextInput::make('delivery_country')
                        ->label('Teslimat ülke')
                        ->disabled()
                        ->visible(fn (DealerRequest $record): bool => (bool) $record->different_delivery_address),
                    Forms\Components\Textarea::make('address')->label('Adres (eski kayıt)')->rows(2)->disabled()->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('İş bilgileri')
                ->schema([
                    Forms\Components\TextInput::make('business_type')->label('İş tipi')->disabled(),
                    Forms\Components\TextInput::make('limited_company_name')->label('Limited şirket adı')->disabled(),
                    Forms\Components\TextInput::make('company_registration_number')->label('Şirket sicil no')->disabled(),
                    Forms\Components\TextInput::make('vat_reg_number')->label('KDV numarası')->disabled(),
                    Forms\Components\TextInput::make('website')->label('Web sitesi')->disabled()->columnSpanFull(),
                    Forms\Components\TextInput::make('facebook')->label('Facebook')->disabled(),
                    Forms\Components\TextInput::make('instagram')->label('Instagram')->disabled(),
                    Forms\Components\TextInput::make('twitter')->label('Twitter')->disabled(),
                    Forms\Components\TextInput::make('linkedin')->label('LinkedIn')->disabled(),
                ])->columns(2),

            Forms\Components\Section::make('Tercihler ve şartlar')
                ->schema([
                    Forms\Components\Placeholder::make('business_profile_display')
                        ->label('Müşteri / satıcı tipi')
                        ->content(fn (DealerRequest $record): ?string => $record->businessProfileLabel()),
                    Forms\Components\Placeholder::make('interest_areas_display')
                        ->label('İlgi alanları')
                        ->content(fn (DealerRequest $record): string => $record->interestAreasLabelled() !== '' ? $record->interestAreasLabelled() : '—'),
                    Forms\Components\TextInput::make('how_heard_about_us')->label('Bizi nereden duydunuz?')->disabled()->columnSpanFull(),
                    Forms\Components\Checkbox::make('terms_accepted')->label('Şartlar kabul edildi')->disabled(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Belgeler')
                ->schema([
                    Forms\Components\Placeholder::make('document_pdf')
                        ->label('PDF')
                        ->content(function (DealerRequest $record): string|HtmlString {
                            if (! $record->document_pdf_path) {
                                return 'Yüklenmedi';
                            }
                            $url = Storage::disk('public')->url($record->document_pdf_path);

                            return new HtmlString('<a class="text-primary-700 underline" target="_blank" href="' . e($url) . '">PDF’i Aç / İndir</a>');
                        }),
                    Forms\Components\Placeholder::make('document_jpeg')
                        ->label('JPEG')
                        ->content(function (DealerRequest $record): string|HtmlString {
                            if (! $record->document_jpeg_path) {
                                return 'Yüklenmedi';
                            }
                            $url = Storage::disk('public')->url($record->document_jpeg_path);

                            return new HtmlString('<a class="text-primary-700 underline" target="_blank" href="' . e($url) . '">JPEG’i Aç / İndir</a>');
                        }),
                ])->columns(2),

            Forms\Components\Section::make('Durum')
                ->schema([
                    Forms\Components\TextInput::make('status')->label('Durum')->disabled(),
                    Forms\Components\DateTimePicker::make('approved_at')->label('Onay Tarihi')->disabled(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'approved' => 'Onaylandı',
                        'rejected' => 'Reddedildi',
                        default => 'Beklemede',
                    })
                    ->color(fn (string $state) => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('business_name')
                    ->label('İşletme')
                    ->searchable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Kişi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Onayla')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (DealerRequest $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (DealerRequest $record, DealerRequestApprover $approver): void {
                        try {
                            $result = $approver->approve($record, auth()->id());
                        } catch (\RuntimeException $e) {
                            Notification::make()
                                ->title($e->getMessage())
                                ->danger()
                                ->send();

                            return;
                        }

                        $body = $result['generated_password']
                            ? 'Kullanıcı oluşturuldu. Şifre: '.$result['generated_password']
                            : 'Kullanıcı oluşturuldu. Başvuruda belirlediği şifre ile /giriş yapabilir.';

                        Notification::make()
                            ->title('Bayilik talebi onaylandı')
                            ->body($body)
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDealerRequests::route('/'),
            'view' => Pages\ViewDealerRequest::route('/{record}'),
        ];
    }
}
