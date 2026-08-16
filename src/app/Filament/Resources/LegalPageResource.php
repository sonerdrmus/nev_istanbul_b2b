<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LegalPageResource\Pages;
use App\Models\LegalPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class LegalPageResource extends Resource
{
    protected static ?string $model = LegalPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'E-Ticaret';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Sayfa';

    protected static ?string $pluralModelLabel = 'Sayfalar';

    protected static ?string $navigationLabel = 'Sayfalar';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationSort(): ?int
    {
        return 11;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Sayfa')
                    ->schema([
                        Forms\Components\TextInput::make('slug')
                            ->label('URL slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Web adresi: /sozlesme/slug'),
                        Forms\Components\Toggle::make('is_published')
                            ->label('Sitede yayınla')
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(3),
                Forms\Components\Tabs::make('languages')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Türkçe')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Başlık (TR)')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, ?string $state): void {
                                        if (filled($get('slug'))) {
                                            return;
                                        }
                                        $set('slug', Str::slug($state ?? ''));
                                    }),
                                Forms\Components\RichEditor::make('body')
                                    ->label('Metin (TR)')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('English')
                            ->schema([
                                Forms\Components\TextInput::make('title_en')
                                    ->label('Title (EN)')
                                    ->maxLength(255),
                                Forms\Components\RichEditor::make('body_en')
                                    ->label('Content (EN)')
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Italiano')
                            ->schema([
                                Forms\Components\TextInput::make('title_it')
                                    ->label('Titolo (IT)')
                                    ->maxLength(255),
                                Forms\Components\RichEditor::make('body_it')
                                    ->label('Testo (IT)')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Yayında')
                    ->boolean(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLegalPages::route('/'),
            'create' => Pages\CreateLegalPage::route('/create'),
            'edit' => Pages\EditLegalPage::route('/{record}/edit'),
        ];
    }
}
