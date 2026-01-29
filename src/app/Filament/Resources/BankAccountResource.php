<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankAccountResource\Pages;
use App\Models\BankAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'B2B Yönetimi';

    protected static ?string $modelLabel = 'Banka Hesabı';

    protected static ?string $pluralModelLabel = 'Banka Hesapları';

    protected static ?string $recordTitleAttribute = 'bank_name';

    protected static ?string $navigationLabel = 'Banka Bilgileri';

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Hesap Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('bank_name')
                            ->label('Banka Adı')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ziraat Bankası'),
                        Forms\Components\TextInput::make('branch')
                            ->label('Şube')
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\TextInput::make('iban')
                            ->label('IBAN')
                            ->required()
                            ->maxLength(34)
                            ->placeholder('TR00 0000 0000 0000 0000 0000 00'),
                        Forms\Components\TextInput::make('account_holder')
                            ->label('Hesap Sahibi / Unvan')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('currency')
                            ->label('Para Birimi')
                            ->default('TRY')
                            ->maxLength(10),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Footer\'da gösterilsin'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bank_name')
                    ->label('Banka')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('branch')
                    ->label('Şube')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('iban')
                    ->label('IBAN')
                    ->searchable()
                    ->limit(24),
                Tables\Columns\TextColumn::make('account_holder')
                    ->label('Hesap Sahibi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif'),
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
            'index' => Pages\ListBankAccounts::route('/'),
            'create' => Pages\CreateBankAccount::route('/create'),
            'edit' => Pages\EditBankAccount::route('/{record}/edit'),
        ];
    }
}
