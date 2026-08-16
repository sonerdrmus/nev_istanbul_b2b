<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FooterMenuGroupResource\Pages;
use App\Models\FooterMenuGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FooterMenuGroupResource extends Resource
{
    protected static ?string $model = FooterMenuGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'E-Ticaret';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Footer Menü Grubu';

    protected static ?string $pluralModelLabel = 'Footer Menü';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'Footer Menü';

    public static function getNavigationSort(): ?int
    {
        return 9;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Grup')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Başlık')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Sözleşmeler'),
                        Forms\Components\Select::make('type')
                            ->label('Tip')
                            ->options([
                                FooterMenuGroup::TYPE_MENU => 'Menü (link listesi)',
                                FooterMenuGroup::TYPE_CATEGORIES => 'Kategoriler (üst grup)',
                                FooterMenuGroup::TYPE_BANK_INFO => 'Banka Bilgileri',
                            ])
                            ->default(FooterMenuGroup::TYPE_MENU)
                            ->required()
                            ->live()
                            ->helperText('Kategoriler: sitedeki kategoriler otomatik listelenir. Banka Bilgileri: Banka Hesapları kayıtları gösterilir.'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Menü Linkleri')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('Metin')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Teslimat bilgisi ve maliyetler'),
                                Forms\Components\TextInput::make('url')
                                    ->label('URL')
                                    ->maxLength(500)
                                    ->default('#')
                                    ->placeholder('/sayfa/slug veya https://...'),
                                Forms\Components\Toggle::make('open_in_new_tab')
                                    ->label('Yeni sekmede aç')
                                    ->default(false),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Sıra')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Link ekle')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->visible(fn (Forms\Get $get): bool => $get('type') === FooterMenuGroup::TYPE_MENU),
                    ])
                    ->visible(fn (Forms\Get $get): bool => $get('type') === FooterMenuGroup::TYPE_MENU),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tip')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        FooterMenuGroup::TYPE_MENU => 'Menü',
                        FooterMenuGroup::TYPE_CATEGORIES => 'Kategoriler',
                        FooterMenuGroup::TYPE_BANK_INFO => 'Banka Bilgileri',
                        default => $state,
                    })
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Link')
                    ->counts('items')
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
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
            'index' => Pages\ListFooterMenuGroups::route('/'),
            'create' => Pages\CreateFooterMenuGroup::route('/create'),
            'edit' => Pages\EditFooterMenuGroup::route('/{record}/edit'),
        ];
    }
}
