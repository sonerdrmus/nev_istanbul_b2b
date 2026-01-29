<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DealerRequestResource\Pages;
use App\Models\Company;
use App\Models\DealerRequest;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
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
            Forms\Components\Section::make('Bayilik Talebi')
                ->schema([
                    Forms\Components\TextInput::make('full_name')->label('Ad Soyad')->disabled(),
                    Forms\Components\TextInput::make('tc_no')->label('T.C.')->disabled(),
                    Forms\Components\TextInput::make('email')->label('E-posta')->disabled(),
                    Forms\Components\TextInput::make('phone')->label('Telefon')->disabled(),
                    Forms\Components\Textarea::make('address')->label('Adres')->rows(3)->disabled()->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Ad Soyad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tc_no')
                    ->label('T.C.')
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
                    ->action(function (DealerRequest $record): void {
                        if ($record->status !== 'pending') {
                            return;
                        }
                        if (User::where('email', $record->email)->exists()) {
                            Notification::make()
                                ->title('Bu e-posta ile kullanıcı zaten var')
                                ->danger()
                                ->send();
                            return;
                        }

                        $passwordPlain = Str::random(10);
                        do {
                            $code = 'BAYI-' . strtoupper(Str::random(6));
                        } while (Company::where('code', $code)->exists());

                        $company = Company::create([
                            'name' => $record->full_name,
                            'code' => $code,
                            'is_active' => true,
                        ]);

                        $user = User::create([
                            'company_id' => $company->id,
                            'name' => $record->full_name,
                            'email' => $record->email,
                            'password' => $passwordPlain,
                            'is_admin' => false,
                        ]);

                        $record->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                            'created_company_id' => $company->id,
                            'created_user_id' => $user->id,
                        ]);

                        Notification::make()
                            ->title('Bayilik talebi onaylandı')
                            ->body("Kullanıcı oluşturuldu. Şifre: {$passwordPlain}")
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

