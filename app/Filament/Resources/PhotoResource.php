<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PhotoResource\Pages;
use App\Filament\Resources\PhotoResource\RelationManagers;
use App\Models\Photo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use App\Models\Article;

class PhotoResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()->can('manage_photos') || auth()->user()->can('admin');
    }
    
    protected static ?string $model = Photo::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Ogólne';
    protected static ?string $modelLabel = 'Zdjęcie';
    protected static ?string $pluralModelLabel = 'Zdjęcia';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Card::make()
                            ->schema([
                                FileUpload::make('image_path')
                                    ->image()
                                    ->imageEditor()
                                    ->disk('public')
                                    ->directory('photos')
                                    ->label('Zdjęcie')
                                    ->required(),
                                TextInput::make('image_name')
                                    ->label('Nazwa')
                                    ->required(),
                                TextInput::make('image_desc')
                                    ->label('Opis (wykorzystywany w SEO)')
                                    ->required(),
                            ]),
                        Card::make()
                            ->schema([
                                Select::make('articles')
                                    ->multiple()
                                    ->label('Przypięte artykuły')
                                    ->nullable()
                                    ->placeholder('Wybierz artykuły')
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->relationship('articles', 'title', fn ($query) => $query->whereIn('type', ['article', 'article-with-map'])),
                            ]),
                        Card::make()
                            ->schema([
                                Select::make('articlesNews')
                                    ->multiple()
                                    ->label('Przypięte aktualności')
                                    ->nullable()
                                    ->placeholder('Wybierz aktualności')
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->relationship('articlesNews', 'title'),
                            ]),
                        // Card::make()
                        //     ->schema([
                        //         Select::make('jobOffers')
                        //             ->multiple()
                        //             ->label('Pinned job offers')
                        //             ->nullable()
                        //             ->placeholder('Select job offers')
                        //             ->searchable()
                        //             ->preload()
                        //             ->reactive()
                        //             ->relationship('jobOffers', 'title'),
                        //     ]),
                ])->columnSpan(8),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('image_name')->label('Nazwa')->limit(35),
                Tables\Columns\TextColumn::make('image_desc')->label('Opis')->limit(35),
                Tables\Columns\ImageColumn::make('image_path')->label('Zdjęcie'),
            ])
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
            'index' => Pages\ListPhotos::route('/'),
            'create' => Pages\CreatePhoto::route('/create'),
            'view' => Pages\ViewPhoto::route('/{record}'),
            'edit' => Pages\EditPhoto::route('/{record}/edit'),
        ];
    }
}
