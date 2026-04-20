<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use App\Filament\Resources\ProductResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $title = 'Bu kategorideki ürünler';

    protected static ?string $recordTitleAttribute = 'name';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Liste sırası')
                    ->sortable()
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Ürün adı')
                    ->searchable()
                    ->weight(\Filament\Support\Enums\FontWeight::Medium),
            ])
            ->defaultSort('sort_order', 'asc')
            ->paginated([10, 25, 50])
            ->headerActions([
                Tables\Actions\CreateAction::make()->hidden(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Sıra ve ürünü düzenle')
                    ->url(fn ($record) => ProductResource::getUrl('edit', ['record' => $record])),
            ])
            ->bulkActions([]);
    }
}
