<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InterfaceLabelTypeVariationResource\Pages;
use App\Models\InterfaceLabelTypeVariation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Ana menü bağlantısı {@see \App\Providers\Filament\AdminPanelProvider} içinde özelleştirilir.
 */
class InterfaceLabelTypeVariationResource extends Resource
{
    protected static ?string $model = InterfaceLabelTypeVariation::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Varyasyon yönetimi';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Etiket türü';

    protected static ?string $pluralModelLabel = 'Etiket türleri';

    protected static ?string $navigationLabel = 'Etiket Türü Yönetimi';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Etiket türü')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Etiket adı')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Görsel')
                            ->directory('interface_label_type_variations')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->nullable()
                            ->helperText('Etiket önizleme görseli.'),
                        Forms\Components\Checkbox::make('is_custom_print')
                            ->label('Özel baskı seçeneği')
                            ->default(false),
                        Forms\Components\Fieldset::make('Konum')
                            ->schema([
                                Forms\Components\Checkbox::make('position_front')
                                    ->label('Ön')
                                    ->default(false),
                                Forms\Components\Checkbox::make('position_back')
                                    ->label('Arka')
                                    ->default(false),
                            ])
                            ->columns(2),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0)
                            ->helperText('Listede önce gelmesi için küçük sayı. Sürükleyerek de sıralayabilirsiniz.'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Yayında')
                            ->default(true),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Görsel')
                    ->disk('public')
                    ->visibility('public')
                    ->height(50),
                Tables\Columns\TextColumn::make('name')
                    ->label('Etiket adı')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_custom_print')
                    ->label('Özel baskı')
                    ->boolean(),
                Tables\Columns\IconColumn::make('position_front')
                    ->label('Ön')
                    ->boolean(),
                Tables\Columns\IconColumn::make('position_back')
                    ->label('Arka')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Yayında')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellendi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Yayında'),
                Tables\Filters\TernaryFilter::make('is_custom_print')
                    ->label('Özel baskı'),
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
            'index' => Pages\ListInterfaceLabelTypeVariations::route('/'),
            'create' => Pages\CreateInterfaceLabelTypeVariation::route('/create'),
            'edit' => Pages\EditInterfaceLabelTypeVariation::route('/{record}/edit'),
        ];
    }
}
