<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Currency;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'B2B Yönetimi';

    protected static ?string $navigationLabel = 'Bayi Listesi';

    protected static ?string $modelLabel = 'Kullanıcı';

    protected static ?string $pluralModelLabel = 'Kullanıcılar';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Kullanıcı Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Ad Soyad')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('E-posta')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Select::make('company_id')
                            ->label('Şirket')
                            ->relationship('company', 'name', fn (Builder $query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\TextInput::make('company_profit_margin_percentage')
                            ->label('Kâr marjı (indirim) %')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->default(0)
                            ->visible(fn ($get, ?User $record = null) => $get('company_id') || ($record?->company_id !== null))
                            ->helperText('Bu müşterinin şirketine uygulanan indirim oranı. Mağazada ürün fiyatlarına yansır. Sadece şirket atanmış kullanıcılarda görünür.'),
                        Forms\Components\Toggle::make('is_admin')
                            ->label('Admin')
                            ->default(false),
                        Forms\Components\Select::make('visible_currency_ids')
                            ->label('Görünecek para birimleri')
                            ->options(fn () => Currency::active()->orderBy('sort_order')->pluck('name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->nullable()
                            ->visible(fn ($get, ?User $record = null) => ($get('company_id') || $record?->company_id) && ! ($get('is_admin') ?? $record?->is_admin ?? false))
                            ->helperText('Boş bırakılırsa bu bayi mağazada tüm para birimlerini görür. Sadece bayi (şirket atanmış, admin olmayan) kullanıcılarda geçerlidir.'),
                        Forms\Components\TextInput::make('password')
                            ->label(fn (string $operation): string => $operation === 'create' ? 'Şifre' : 'Yeni Şifre (boş bırakılırsa değişmez)')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Şirket')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_admin')
                    ->label('Admin')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company_id')
                    ->label('Şirket')
                    ->relationship('company', 'name'),
                Tables\Filters\TernaryFilter::make('is_admin')
                    ->label('Admin'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
