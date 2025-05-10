<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttachmentResource\Pages;
use App\Filament\Resources\AttachmentResource\RelationManagers;
use App\Models\Attachment;
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

class AttachmentResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()->can('manage_attachments') || auth()->user()->can('admin');
    }
    
    protected static ?string $model = Attachment::class;
    protected static ?string $navigationIcon = 'heroicon-o-paper-clip';
    protected static ?string $navigationGroup = 'Ogólne';
    protected static ?string $modelLabel = 'Załącznik';
    protected static ?string $pluralModelLabel = 'Załączniki';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Card::make()
                            ->schema([
                                FileUpload::make('file_path')
                                    ->disk('public')
                                    ->directory('attachments')
                                    ->label('Załącznik')
                                    ->required(),
                                TextInput::make('file_name')
                                    ->label('Nazwa')
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
                        Card::make()
                            ->schema([
                                Select::make('jobOffers')
                                    ->multiple()
                                    ->label('Przypięte oferty pracy')
                                    ->nullable()
                                    ->placeholder('Wybierz oferty pracy')
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->relationship('jobOffers', 'title'),
                            ]),
                    ])->columnSpan(8),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('file_name')->label('Nazwa')
                    ->limit(35)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        
                        if (strlen($state) <= 35) {
                            return null;
                        }
                        
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('file_path')->label('Załącznik'),
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
            'index' => Pages\ListAttachments::route('/'),
            'create' => Pages\CreateAttachment::route('/create'),
            'view' => Pages\ViewAttachment::route('/{record}'),
            'edit' => Pages\EditAttachment::route('/{record}/edit'),
        ];
    }
}
