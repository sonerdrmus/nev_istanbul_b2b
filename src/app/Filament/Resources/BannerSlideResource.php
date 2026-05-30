<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerSlideResource\Pages;
use App\Models\BannerSlide;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BannerSlideResource extends Resource
{
    protected static ?string $model = BannerSlide::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'E-Ticaret';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Banner Slayt';

    protected static ?string $pluralModelLabel = 'Banner Slaytlar';

    protected static ?string $recordTitleAttribute = 'headline';

    public static function getNavigationSort(): ?int
    {
        return 7;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Görsel')
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Slayt görseli')
                            ->image()
                            ->directory('banner_slides')
                            ->visibility('public')
                            ->imagePreviewHeight('200')
                            ->helperText('Önerilen boyut: 1024×278 px (geniş banner, ~3.7:1). Görsel slayt alanını tam doldurur; metin alanları boş bırakılabilir.')
                            ->nullable(),
                    ]),
                Forms\Components\Section::make('Metinler')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Üst etiket')
                            ->placeholder('Örn: Yeni Sezon')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('headline')
                            ->label('Ana başlık')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(3)
                            ->maxLength(500),
                        Forms\Components\TextInput::make('button_text')
                            ->label('Buton metni')
                            ->placeholder('Örn: Alışverişe Başla')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('button_url')
                            ->label('Buton linki (URL)')
                            ->placeholder('https://... veya /sepet')
                            ->maxLength(500),
                        Forms\Components\Select::make('text_align')
                            ->label('Metin hizası')
                            ->options([
                                'left' => 'Sol',
                                'center' => 'Orta',
                                'right' => 'Sağ',
                            ])
                            ->default('left'),
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
                Tables\Columns\TextColumn::make('headline')
                    ->label('Başlık')
                    ->formatStateUsing(fn (?string $state, BannerSlide $record): string => filled($state)
                        ? $state
                        : (filled($record->title) ? $record->title : '—'))
                    ->limit(40)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Etiket')
                    ->limit(20)
                    ->toggleable(),
                Tables\Columns\SelectColumn::make('text_align')
                    ->label('Hiza')
                    ->options(['left' => 'Sol', 'center' => 'Orta', 'right' => 'Sağ'])
                    ->toggleable(),
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
            'index' => Pages\ListBannerSlides::route('/'),
            'create' => Pages\CreateBannerSlide::route('/create'),
            'edit' => Pages\EditBannerSlide::route('/{record}/edit'),
        ];
    }
}
