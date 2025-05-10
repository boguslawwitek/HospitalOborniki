<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectTypeResource\Pages;
use App\Filament\Resources\ProjectTypeResource\RelationManagers;
use App\Models\ProjectType;
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

class ProjectTypeResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()->can('manage_projects') || auth()->user()->can('admin');
    }
    
    protected static ?string $model = ProjectType::class;

    protected static ?string $navigationGroup = 'Projekty';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $modelLabel = 'Rodzaj dofinansowania';
    protected static ?string $pluralModelLabel = 'Rodzaje dofinansowań';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->extraAttributes(['style' => 'gap:0.5rem'])
            ->schema([
                Grid::make()
                    ->schema([
                        Card::make()
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Nazwa')
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
                            ]),
                    ])->columnSpan(8),
                Grid::make()
                    ->schema([
                        Card::make()
                            ->schema([
                                Select::make('projectArticles')
                                    ->multiple()
                                    ->required()
                                    ->label('Przypięte dofinansowania')
                                    ->nullable()
                                    ->placeholder('Wybierz dofinansowania')
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->relationship('projectArticles', 'title'),
                            ]),
                    ])->columnSpan(8),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Nazwa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data dodania')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Data aktualizacji')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
            'index' => Pages\ListProjectTypes::route('/'),
            'create' => Pages\CreateProjectType::route('/create'),
            'view' => Pages\ViewProjectType::route('/{record}'),
            'edit' => Pages\EditProjectType::route('/{record}/edit'),
        ];
    }
}
