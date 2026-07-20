<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InterfaceColorVariationResource\Pages;
use App\Models\InterfaceColorVariation;
use App\Support\InterfaceColorSwatchGenerator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

/**
 * Ana menü bağlantısı {@see \App\Providers\Filament\AdminPanelProvider} içinde özelleştirilir.
 *
 * Kayıtlar mağaza arayüzünde sırayla kullanılmak için: {@see InterfaceColorVariation::forDisplay()}.
 */
class InterfaceColorVariationResource extends Resource
{
    protected static ?string $model = InterfaceColorVariation::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $modelLabel = 'Renk varyasyonu';

    protected static ?string $pluralModelLabel = 'Renk varyasyonları';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Renk varyasyonu')
                    ->description('Kumaş bağlantısı Kumaş Türü Varyasyonları sayfasından yapılır.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Renk adı (opsiyonel)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hex_color')
                            ->label('Renk kodu (hex)')
                            ->placeholder('#FFFFFF')
                            ->maxLength(7)
                            ->regex('/^#[0-9A-Fa-f]{6}$/')
                            ->validationMessages([
                                'regex' => 'Geçerli bir hex renk kodu girin (örn. #FFFFFF).',
                            ])
                            ->nullable()
                            ->helperText('Renk görseli yüklemeden düz renk tanımlamak için hex kodu girin. Görsel veya hex kodundan en az biri zorunludur.'),
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Renk görseli')
                            ->directory('interface_color_variations')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->nullable()
                            ->helperText('Küçük swatch / renk örneği görseli yükleyin. Renk kodu girdiyseniz görsel opsiyoneldir.'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0)
                            ->helperText('Listede önce gelmesi için küçük sayı verin. Sürükleyerek de sıralayabilirsiniz.'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Yayında')
                            ->default(true),
                    ])
                    ->columns(1),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('fabricTypeVariations');
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
                Tables\Columns\TextColumn::make('hex_color')
                    ->label('Hex')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Ad'),
                Tables\Columns\TextColumn::make('fabricTypeVariations.name')
                    ->label('Bağlı kumaşlar')
                    ->badge()
                    ->placeholder('—')
                    ->toggleable(),
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
                Tables\Filters\SelectFilter::make('fabricTypeVariations')
                    ->label('Bağlı kumaş')
                    ->relationship('fabricTypeVariations', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
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

    /**
     * Form kaydı öncesi: görsel veya hex zorunluluğu; yalnızca hex varsa swatch PNG üretir.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function finalizeFormData(array $data): array
    {
        $imagePath = static::resolveImagePathFromFormState($data['image_path'] ?? null);
        $hex = trim((string) ($data['hex_color'] ?? ''));

        if ($imagePath === '' && $hex === '') {
            throw ValidationException::withMessages([
                'data.image_path' => 'Renk görseli veya renk kodundan en az biri zorunludur.',
                'data.hex_color' => 'Renk görseli veya renk kodundan en az biri zorunludur.',
            ]);
        }

        if ($hex !== '') {
            try {
                $data['hex_color'] = InterfaceColorSwatchGenerator::normalizeHex($hex);
            } catch (InvalidArgumentException $exception) {
                throw ValidationException::withMessages([
                    'data.hex_color' => $exception->getMessage(),
                ]);
            }
        } else {
            $data['hex_color'] = null;
        }

        if ($imagePath === '' && $hex !== '') {
            $relativePath = InterfaceColorSwatchGenerator::relativePathForHex($hex);
            $absolutePath = storage_path('app/public/'.$relativePath);
            InterfaceColorSwatchGenerator::writePng($hex, $absolutePath);
            $data['image_path'] = $relativePath;
        } elseif ($imagePath !== '') {
            $data['image_path'] = $imagePath;
        }

        return $data;
    }

    private static function resolveImagePathFromFormState(mixed $state): string
    {
        if ($state === null || $state === '') {
            return '';
        }

        if (is_array($state)) {
            $state = $state[array_key_first($state)] ?? '';
        }

        return is_string($state) ? trim($state) : '';
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInterfaceColorVariations::route('/'),
            'create' => Pages\CreateInterfaceColorVariation::route('/create'),
            'edit' => Pages\EditInterfaceColorVariation::route('/{record}/edit'),
        ];
    }
}
