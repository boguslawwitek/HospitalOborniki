<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectArticleResource\Pages;
use App\Filament\Resources\ProjectArticleResource\RelationManagers;
use App\Models\ProjectArticle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Illuminate\Support\Str;
use Filament\Forms\Set;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;

class ProjectArticleResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()->can('manage_projects') || auth()->user()->can('admin');
    }
    
    protected static ?string $model = ProjectArticle::class;

    protected static ?string $navigationGroup = 'Projekty';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $modelLabel = 'Dofinansowanie';
    protected static ?string $pluralModelLabel = 'Dofinansowania';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->extraAttributes(['style' => 'gap:0.5rem'])
            ->schema([
                Card::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('title')
                                    ->label('Nazwa')
                                    ->required()
                                    ->maxLength(2048)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        $set('slug', Str::slug($state));
                                    }),
                                TextInput::make('slug')
                                    ->label('Przyjazny link')
                                    ->required()
                                    ->maxLength(2048),
                            ]),
                                FileUpload::make('logo')
                                    ->image()
                                    ->imageEditor()
                                    ->label('Logo')
                                    ->default(null),
                                RichEditor::make('body')
                                    ->label('Treść')
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('project-content')
                                    ->columnSpanFull(),
                                Toggle::make('active')
                                    ->label('Aktywny')
                                    ->required(),
                                DateTimePicker::make('published_at')
                                    ->label('Data publikacji')
                                    ->required(),
                    ])->columnSpan(8),
                Card::make()
                    ->schema([
                        Select::make('projectTypes')
                            ->multiple()
                            ->required()
                            ->label('Rodzaje dofinansowań')
                            ->nullable()
                            ->placeholder('Wybierz rodzaje dofinansowań')
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->relationship('projectTypes', 'title'),
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
                    ])->columnSpan(4),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Nazwa')
                    ->searchable()
                    ->limit(35)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        
                        if (strlen($state) <= 35) {
                            return null;
                        }
                        
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('projectTypes.title')
                    ->label('Kategorie')
                    ->limit(30)
                    ->listWithLineBreaks()
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        
                        if (is_array($state) && strlen(implode(', ', $state)) <= 30) {
                            return null;
                        }
                        
                        return is_array($state) ? implode(", ", $state) : $state;
                    }),
                Tables\Columns\IconColumn::make('active')
                    ->label('Aktywny')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Data publikacji')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
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
            'index' => Pages\ListProjectArticles::route('/'),
            'create' => Pages\CreateProjectArticle::route('/create'),
            'view' => Pages\ViewProjectArticle::route('/{record}'),
            'edit' => Pages\EditProjectArticle::route('/{record}/edit'),
        ];
    }
}
