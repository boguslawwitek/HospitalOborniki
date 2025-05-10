<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Resources\ArticleResource\RelationManagers;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use App\Models\Attachment;
use App\Models\Photo;
use Illuminate\Support\Facades\Storage;

class ArticleResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()->can('manage_articles') || auth()->user()->can('admin');
    }
    
    protected static ?string $model = Article::class;

    protected static ?string $navigationGroup = 'Treści';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Artykuł / Odnośnik';
    protected static ?string $pluralModelLabel = 'Artykuły / Odnośniki';
    protected static ?int $navigationSort = 1;

    protected static ?array $articleTypes = ['article', 'article-with-map'];

    public static function form(Form $form): Form
    {
        return $form
            ->extraAttributes(['style' => 'gap:0.5rem'])
            ->schema([
                Card::make()
                    ->schema([
                        // SELECT TYPE
                        Select::make('type')
                            ->label("Rodzaj artykułu")
                            ->required()
                            ->default('article')
                            ->options([
                                'article' => 'Artykuł',
                                'article-with-map' => 'Artykuł z mapą lokalizacyjną',
                                'link' => 'Odnośnik',
                            ])
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state === 'link') {
                                    $set('slug', null);
                                    $set('body', null);
                                    $set('active', false);
                                    $set('external', false);
                                    $set('thumbnail', null);
                                    $set('attachments', null);
                                    $set('photos', null);
                                    $set('employees', null);
                                    $set('additional_body', null);
                                    $set('map_body', null);
                                } elseif (in_array($state, static::$articleTypes)) {
                                    $set('body', null);
                                    $set('active', false);
                                    $set('external', false);
                                    $set('attachments', null);
                                    $set('photos', null);
                                    $set('employees', null);
                                    $set('additional_body', null);
                                    $set('map_body', null);
                                }
                            }),
                        // END SELECT TYPE
                        // LINK TYPE
                        Card::make()
                            ->hidden(fn ($get): bool => $get('type') != 'link')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Tytuł')
                                    ->required()
                                    ->maxLength(2048),
                                TextInput::make('slug')
                                    ->label('Ścieżka lub URL (np. /aktualnosci lub https://szpital.oborniki.info)')
                                    ->required()
                                    ->maxLength(2048),
                                Select::make('categories')
                                    ->multiple()
                                    ->label('Kategorie')
                                    ->nullable()
                                    ->placeholder('Wybierz kategorię')
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->relationship('categories', 'title'),
                                DateTimePicker::make('published_at')
                                    ->label('Data publikacji')
                                    ->native(false)
                                    ->displayFormat('d.m.Y')
                                    ->closeOnDateSelection()
                                    ->timezone('Europe/Warsaw'),
                                Grid::make()
                                    ->schema([
                                        Toggle::make('active')
                                            ->label('Aktywny')
                                            ->required(),
                                        Toggle::make('external')
                                            ->label('Otwieraj w nowej karcie')
                                            ->required(),
                                    ]),
                            ]),
                        // END LINK TYPE
                        // ARTICLE TYPE
                        Grid::make()
                            ->hidden(fn ($get): bool => !in_array($get('type'), static::$articleTypes))
                            ->schema([
                                TextInput::make('title')
                                    ->label('Tytuł')
                                    ->required()
                                    ->maxLength(2048)
                                    //->reactive() // this works without waiting but very slowly
                                    //->live(debounce: 500) // wait 500 ms before update
                                    ->live(onBlur: true) // Update only after focus on other element
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        $set('slug', Str::slug($state));
                                    }),
                                TextInput::make('slug')
                                    ->label('Przyjazny link')
                                    ->required()
                                    ->maxLength(2048),
                            ]),
                        RichEditor::make('body')
                            ->label('Treść')
                            ->hidden(fn ($get): bool => !in_array($get('type'), static::$articleTypes))
                            ->required()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('article-content')
                            ->columnSpanFull(),
                        RichEditor::make('additional_body')
                            ->label('Dodatkowa treść')
                            ->hidden(fn ($get): bool => !in_array($get('type'), static::$articleTypes))
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('article-additional-content')
                            ->columnSpanFull(),
                        RichEditor::make('map_body')
                            ->label('Lokalizacja (widoczna pod mapą budynków)')
                            ->hidden(fn ($get): bool => $get('type') != 'article-with-map')
                            ->required()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('article-map-content')
                            ->columnSpanFull(),
                        Toggle::make('active')
                            ->label('Aktywny')
                            ->hidden(fn ($get): bool => !in_array($get('type'), static::$articleTypes))
                            ->required(),
                        DateTimePicker::make('published_at')
                            ->hidden(fn ($get): bool => !in_array($get('type'), static::$articleTypes))
                            ->label('Data publikacji')
                            ->native(false)
                            ->displayFormat('d.m.Y')
                            ->closeOnDateSelection()
                            ->timezone('Europe/Warsaw'),
                    ])->columnSpan(8),
                Card::make()
                    ->hidden(fn ($get): bool => !in_array($get('type'), static::$articleTypes))
                    ->schema([
                        FileUpload::make('thumbnail')
                        ->hidden(fn ($get): bool => $get('type') != 'article')
                            ->label('Obraz')
                            ->disk('public')
                            ->directory('thumbnails')
                            ->default(null),
                        Select::make('categories')
                            ->multiple()
                            ->label('Kategorie')
                            ->nullable()
                            ->placeholder('Wybierz kategorię')
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->relationship('categories', 'title'),
                    ])->columnSpan(4),
                Grid::make()
                    ->hidden(fn ($get): bool => !in_array($get('type'), static::$articleTypes))
                    ->schema([
                        Card::make()
                            ->schema([
                                Select::make('attachments')
                                    ->multiple()
                                    ->required()
                                    ->label('Przypięte załączniki')
                                    ->nullable()
                                    ->placeholder('Wybierz załączniki')
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->relationship('attachments', 'file_name'),
                            ]),
                        Card::make()
                            ->schema([
                                Select::make('photos')
                                    ->multiple()
                                    ->required()
                                    ->label('Przypięte zdjęcia')
                                    ->nullable()
                                    ->placeholder('Wybierz zdjęcia')
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->relationship('photos', 'image_name'),
                            ]),
                    ])->columnSpan(8),
                // END ARTICLE TYPE
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Rodzaj')
                    ->formatStateUsing(function ($state) {
                        return $state === 'article' ? 'Artykuł' : ($state === 'article-with-map' ? 'Artykuł z mapą' : 'Odnośnik');
                    }),
                TextColumn::make('title')
                    ->label('Tytuł')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('thumbnail')
                    ->label('Obraz'),
                IconColumn::make('active')
                    ->label('Aktywny')
                    ->boolean(),
                TextColumn::make('published_at')
                    ->label('Data publikacji')
                    ->dateTime('d.m.Y')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Twórca')
                    ->numeric(),
                TextColumn::make('updated_at')
                    ->label('Data aktualizacji')
                    ->dateTime('d.m.Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->description('W tym miejscu możesz dodać, edytować oraz usuwać artykuły oraz odnośniki.')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'view' => Pages\ViewArticle::route('/{record}'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
