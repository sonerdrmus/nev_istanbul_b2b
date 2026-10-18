<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'E-Ticaret';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Makale';

    protected static ?string $pluralModelLabel = 'Makaleler';

    protected static ?string $navigationLabel = 'Blog';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Yayın')
                    ->schema([
                        Forms\Components\TextInput::make('slug')
                            ->label('URL slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Web adresi: /blog/slug'),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Yayın tarihi')
                            ->seconds(false)
                            ->default(now()),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Forms\Components\Toggle::make('is_published')
                            ->label('Sitede yayınla')
                            ->default(true),
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Kapak görseli')
                            ->image()
                            ->directory('blog_posts')
                            ->visibility('public')
                            ->imagePreviewHeight('160')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
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
                                Forms\Components\Textarea::make('excerpt')
                                    ->label('Kısa özet (TR)')
                                    ->rows(3)
                                    ->helperText('Ana sayfa kartında görünür. Boşsa metnin başı kullanılır.'),
                                Forms\Components\RichEditor::make('body')
                                    ->label('Makale (TR)')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('English')
                            ->schema([
                                Forms\Components\TextInput::make('title_en')
                                    ->label('Title (EN)')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('excerpt_en')
                                    ->label('Excerpt (EN)')
                                    ->rows(3),
                                Forms\Components\RichEditor::make('body_en')
                                    ->label('Article (EN)')
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Italiano')
                            ->schema([
                                Forms\Components\TextInput::make('title_it')
                                    ->label('Titolo (IT)')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('excerpt_it')
                                    ->label('Riassunto (IT)')
                                    ->rows(3),
                                Forms\Components\RichEditor::make('body_it')
                                    ->label('Articolo (IT)')
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
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Görsel')
                    ->disk('public')
                    ->height(48),
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Yayında')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Yayın tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
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
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
