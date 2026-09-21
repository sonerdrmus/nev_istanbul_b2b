<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'B2B Yönetimi';

    protected static ?string $modelLabel = 'Sipariş';

    protected static ?string $pluralModelLabel = 'Siparişler';

    protected static ?string $recordTitleAttribute = 'order_number';

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Order::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Sipariş Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('Sipariş No')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options([
                                'pending' => 'Beklemede',
                                'paid' => 'Ödendi',
                                'cancelled' => 'İptal',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Müşteri Adı')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_email')
                            ->label('E-posta')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_phone')
                            ->label('Telefon')
                            ->maxLength(50),
                        Forms\Components\Textarea::make('customer_address')
                            ->label('Adres / Not')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('total')
                            ->label('Toplam (₺)')
                            ->disabled()
                            ->prefix('₺'),
                        Forms\Components\Select::make('shipping_method_id')
                            ->label('Kargo')
                            ->relationship('shippingMethod', 'name')
                            ->nullable()
                            ->disabled(),
                        Forms\Components\TextInput::make('shipping_cost')
                            ->label('Kargo Ücreti (₺)')
                            ->numeric()
                            ->disabled()
                            ->prefix('₺')
                            ->default(0),
                        Forms\Components\Select::make('bank_account_id')
                            ->label('Havale Bankası')
                            ->relationship('bankAccount', 'bank_name')
                            ->nullable()
                            ->disabled(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Sipariş Notu')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Sipariş Kalemleri')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('product_name')
                                    ->label('Ürün')
                                    ->disabled(),
                                Forms\Components\TextInput::make('price')
                                    ->label('Birim Fiyat')
                                    ->disabled()
                                    ->prefix('₺'),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Adet')
                                    ->disabled()
                                    ->numeric(),
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Ara Toplam')
                                    ->disabled()
                                    ->prefix('₺'),
                            ])
                            ->columns(4)
                            ->disabled()
                            ->defaultItems(0),
                    ])
                    ->collapsed(fn ($record) => $record && $record->items->isEmpty()),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make('Sipariş Detayı')
                    ->schema([
                        TextEntry::make('order_number')->label('Sipariş No')->copyable(),
                        TextEntry::make('status')->label('Durum')->badge(),
                        TextEntry::make('created_at')->label('Sipariş Tarihi')->dateTime('d.m.Y H:i'),
                        TextEntry::make('payment_method')
                            ->label('Ödeme Yöntemi')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'havale' => 'Havale / EFT',
                                default => $state ?: '—',
                            }),
                        TextEntry::make('total')->label('Sipariş Toplamı')->money('TRY')->weight('bold'),
                    ])
                    ->columns(3),
                \Filament\Infolists\Components\Section::make('Müşteri Bilgileri')
                    ->schema([
                        TextEntry::make('customer_name')->label('Müşteri'),
                        TextEntry::make('customer_email')->label('E-posta')->copyable(),
                        TextEntry::make('customer_phone')->label('Telefon')->visible(fn ($record): bool => filled($record->customer_phone)),
                        TextEntry::make('customer_address')->label('Adres / Teslimat Notu')->columnSpanFull()->visible(fn ($record): bool => filled($record->customer_address)),
                    ])
                    ->columns(2),
                \Filament\Infolists\Components\Section::make('Teslimat ve Ödeme')
                    ->schema([
                        TextEntry::make('shipping_method.name')->label('Kargo Yöntemi')->visible(fn ($record): bool => filled($record->shipping_method_id)),
                        TextEntry::make('shipping_cost')->label('Kargo Ücreti')->money('TRY')->visible(fn ($record): bool => (float) $record->shipping_cost > 0),
                        TextEntry::make('bankAccount.bank_name')->label('Havale Bankası')->visible(fn ($record): bool => filled($record->bank_account_id)),
                        TextEntry::make('notes')->label('Sipariş Notu')->columnSpanFull()->visible(fn ($record): bool => filled($record->notes)),
                    ])
                    ->columns(2)
                    ->visible(fn ($record): bool => filled($record->shipping_method_id) || (float) $record->shipping_cost > 0 || filled($record->bank_account_id) || filled($record->notes)),
                \Filament\Infolists\Components\Section::make('Sipariş Kalemleri')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                TextEntry::make('product_name')
                                    ->label('Ürün')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->columnSpan(4),
                                TextEntry::make('quantity')
                                    ->label('Adet')
                                    ->badge()
                                    ->color('gray'),
                                TextEntry::make('price')
                                    ->label('Birim Fiyat')
                                    ->money('TRY'),
                                TextEntry::make('subtotal')
                                    ->label('Ara Toplam')
                                    ->money('TRY')
                                    ->weight('bold'),
                                TextEntry::make('variation_data')
                                    ->label('Seçilen varyasyonlar ve alt seçimler')
                                    ->state(fn (OrderItem $record): string => self::formatVariationDataForPanel($record->variation_data))
                                    ->html()
                                    ->columnSpan(4),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    private static function formatVariationDataForPanel(mixed $value): string
    {
        if (! is_array($value) || $value === []) {
            return '<span class="text-gray-500">—</span>';
        }

        $render = function (mixed $current, ?string $label = null) use (&$render): string {
            if (is_array($current)) {
                $items = [];
                foreach ($current as $key => $child) {
                    if (in_array((string) $key, ['extra_price_try', 'price_multiplier'], true)) {
                        continue;
                    }
                    $childLabel = is_int($key) ? null : Str::of((string) $key)->replace('_', ' ')->headline()->toString();
                    $items[] = $render($child, $childLabel);
                }
                if ($items === []) {
                    return '';
                }
                $heading = $label !== null
                    ? '<p class="border-b border-gray-200 px-3 py-2 text-xs font-bold uppercase tracking-wide text-gray-800 dark:border-gray-700 dark:text-gray-100">'.e($label).'</p>'
                    : '';
                return '<div class="overflow-hidden rounded-lg border border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-900">'.$heading.'<div class="space-y-1 px-3 py-2">'.implode('', $items).'</div></div>';
            }

            if ($current === null || $current === '') {
                return '';
            }

            $display = is_bool($current)
                ? ($current ? 'Evet' : 'Hayır')
                : (string) $current;
            $labelHtml = $label !== null ? '<span class="font-semibold text-gray-700 dark:text-gray-300">'.e($label).'</span>' : '';

            return '<div class="grid grid-cols-[minmax(8rem,auto)_minmax(0,1fr)] gap-x-3 border-b border-gray-100 py-1.5 text-sm last:border-b-0 dark:border-gray-700/70">'.$labelHtml.'<span class="break-words font-medium text-gray-950 dark:text-white">'.e($display).'</span></div>';
        };

        $rendered = [];
        foreach ($value as $key => $item) {
            if (in_array((string) $key, ['quick_order', 'product_customization'], true) && $item === null) {
                continue;
            }
            $rendered[] = $render($item, Str::of((string) $key)->replace('_', ' ')->headline()->toString());
        }

        return '<div class="space-y-2 text-left">'.implode('', array_filter($rendered)).'</div>';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Sipariş No')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Müşteri')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_email')
                    ->label('E-posta')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total')
                    ->label('Toplam')
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('shippingMethod.name')
                    ->label('Kargo')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('shipping_cost')
                    ->label('Kargo (₺)')
                    ->money('TRY')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('bankAccount.bank_name')
                    ->label('Havale Bankası')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Ödeme')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'havale' => 'Havale',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Beklemede',
                        'paid' => 'Ödendi',
                        'cancelled' => 'İptal',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Beklemede',
                        'paid' => 'Ödendi',
                        'cancelled' => 'İptal',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
