<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleNewsResource\Pages;
use App\Filament\Resources\ArticleNewsResource\RelationManagers;
use App\Models\ArticleNews;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Card;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;

class ArticleNewsResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()->can('manage_news') || auth()->user()->can('admin');
    }
    
    protected static ?string $model = ArticleNews::class;

    protected static ?string $navigationGroup = 'Ogłoszenia';
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $modelLabel = 'Artykuł';
    protected static ?string $pluralModelLabel = 'Aktualności';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                    Card::make()
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label('Tytuł')
                                ->required()
                                ->maxLength(2048)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Set $set, ?string $state) {
                                    $set('slug', Str::slug($state));
                                }),
                            Forms\Components\TextInput::make('slug')
                                ->label('Przyjazny link')
                                ->required()
                                ->maxLength(2048),
                            Forms\Components\DateTimePicker::make('published_at')
                                ->label('Data publikacji')
                                ->native(false)
                                ->displayFormat('d.m.Y')
                                ->closeOnDateSelection()
                                ->timezone('Europe/Warsaw')
                                ->default(now())
                                ->required(),
                            Forms\Components\FileUpload::make('thumbnail')
                                ->label('Obraz')
                                ->image()
                                ->imageEditor()
                                ->disk('public')
                                ->directory('news-thumbnails')
                                ->default(null),
                            Forms\Components\RichEditor::make('body')
                                ->label('Treść')
                                ->required()
                                ->fileAttachmentsDisk('public')
                                ->fileAttachmentsDirectory('news-content')
                                ->fileAttachmentsVisibility('private')
                                ->columnSpanFull(),
                            Forms\Components\Toggle::make('active')
                                ->label('Aktywny')
                                ->required(),
                        ])
                    ])->columnSpan(8),
                Grid::make()
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Tytuł')
                    ->searchable()
                    ->limit(35)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        
                        if (strlen($state) <= 35) {
                            return null;
                        }
                        
                        return $state;
                    }),
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Obraz')
                    ->searchable(),
                Tables\Columns\IconColumn::make('active')
                    ->label('Aktywny')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Data publikacji')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->description('Aktualności dostępne pod /aktualnosci i /aktualnosci/slug')
            ->filters([
                //
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticleNews::route('/'),
            'create' => Pages\CreateArticleNews::route('/create'),
            'view' => Pages\ViewArticleNews::route('/{record}'),
            'edit' => Pages\EditArticleNews::route('/{record}/edit'),
        ];
    }
}
