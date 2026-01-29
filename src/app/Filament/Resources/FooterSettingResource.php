<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FooterSettingResource\Pages;
use App\Models\FooterSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FooterSettingResource extends Resource
{
    protected static ?string $model = FooterSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'E-Ticaret';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Footer Ayarı';

    protected static ?string $pluralModelLabel = 'Footer Ayarları';

    protected static ?string $navigationLabel = 'Footer Ayarları';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Footer Düzeni')
                    ->description('Footer sütun sayısı ve marka alanı. İçerik Footer Menü gruplarından gelir.')
                    ->schema([
                        Forms\Components\TextInput::make('columns')
                            ->label('Sütun sayısı')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(6)
                            ->default(4)
                            ->required()
                            ->helperText('Menü gruplarının kaç sütunda gösterileceği (varsayılan: 4). Son sütun Banka Bilgileri grubu için kullanılabilir.'),
                        Forms\Components\Toggle::make('show_brand')
                            ->label('Marka alanını göster')
                            ->default(true)
                            ->helperText('Logo ve kısa açıklama bloğu footer\'da görünsün.'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('columns')
                    ->label('Sütun sayısı')
                    ->badge(),
                Tables\Columns\IconColumn::make('show_brand')
                    ->label('Marka göster')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFooterSettings::route('/'),
            'edit' => Pages\EditFooterSetting::route('/{record}/edit'),
        ];
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
