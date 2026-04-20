<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationGroup = 'E-Ticaret';

    protected static ?string $modelLabel = 'Kategori';

    protected static ?string $pluralModelLabel = 'Kategoriler';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Kategori Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('parent_id')
                            ->label('Üst Kategori')
                            ->relationship(
                                'parent',
                                'name',
                                function ($query, ?Category $record) {
                                    $query->with('parentRecursive')->orderBy('sort_order')->orderBy('name');
                                    if ($record) {
                                        $excludeIds = array_merge([$record->id], $record->getDescendantIds());
                                        $query->whereNotIn('id', $excludeIds);
                                    }
                                    return $query;
                                }
                            )
                            ->getOptionLabelFromRecordUsing(fn (Category $record) => $record->full_path)
                            ->searchable()
                            ->preload()
                            ->placeholder('Üst kategori yok (ana kategori)')
                            ->nullable(),
                        Forms\Components\TextInput::make('name')
                            ->label('Kategori Adı')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state ?? ''))),
                        Forms\Components\TextInput::make('slug')
                            ->label('URL Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (Category $record) => str_repeat(' ', $record->depth) . ($record->depth > 0 ? '└ ' : '') . $record->name),
                Tables\Columns\TextColumn::make('full_path')
                    ->label('Tam Yol')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Üst Kategori')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('products_count')
                    ->label('Ürün')
                    ->counts('products')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Üst Kategori')
                    ->relationship('parent', 'name')
                    ->placeholder('Tümü'),
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
        return [
            RelationManagers\ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
