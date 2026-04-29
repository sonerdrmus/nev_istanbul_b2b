<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomeSectionResource\Pages;
use App\Models\HomeSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HomeSectionResource extends Resource
{
    protected static ?string $model = HomeSection::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'E-Ticaret';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Anasayfa Alanı';

    protected static ?string $pluralModelLabel = 'Anasayfa Alanları';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationSort(): ?int
    {
        return 8;
    }

    public static function canViewAny(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Görsel')
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Alan görseli (arka plan)')
                            ->image()
                            ->directory('home_sections')
                            ->visibility('public')
                            ->imagePreviewHeight('200')
                            ->helperText('Yüklerseniz kutuda arka plan olarak gösterilir. Boş bırakırsanız gradient kullanılır.')
                            ->nullable(),
                    ]),
                Forms\Components\Section::make('Metinler')
                    ->schema([
                        Forms\Components\TextInput::make('label')
                            ->label('Üst etiket')
                            ->placeholder('Örn: Kampanya')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('title')
                            ->label('Ana başlık')
                            ->required()
                            ->placeholder('Örn: Üst Giyim')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('subtitle')
                            ->label('Alt metin')
                            ->placeholder('Örn: Tişört, gömlek ve sweatshirt modelleri')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('button_text')
                            ->label('Buton metni')
                            ->placeholder('Örn: İncele')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('link_url')
                            ->label('Link (tıklanınca gidilecek URL)')
                            ->placeholder('https://... veya /?category=tisort')
                            ->maxLength(500)
                            ->helperText('Örn: / veya /?category=tisort'),
                    ])
                    ->columns(1),
                Forms\Components\Section::make('Ayarlar')
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Yayında')
                            ->default(true),
                    ])
                    ->columns(2),
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
                    ->height(50)
                    ->defaultImageUrl(fn ($record) => null),
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('label')
                    ->label('Etiket')
                    ->limit(20)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('subtitle')
                    ->label('Alt metin')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Yayında')
                    ->boolean(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Yayında'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomeSections::route('/'),
            'create' => Pages\CreateHomeSection::route('/create'),
            'edit' => Pages\EditHomeSection::route('/{record}/edit'),
        ];
    }
}
