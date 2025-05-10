<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobOffersResource\Pages;
use App\Filament\Resources\JobOffersResource\RelationManagers;
use App\Models\JobOffers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Forms\Components\Select;

class JobOffersResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()->can('manage_job_offers') || auth()->user()->can('admin');
    }
    
    protected static ?string $model = JobOffers::class;

    protected static ?string $navigationGroup = 'Ogłoszenia';
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $modelLabel = 'Oferta pracy';
    protected static ?string $pluralModelLabel = 'Oferty pracy';
    protected static ?int $navigationSort = 2;

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
                                    ->maxLength(2048),
                                    // ->live(onBlur: true)
                                    // ->afterStateUpdated(function (Set $set, ?string $state) {
                                    //     $set('slug', Str::slug($state));
                                    // }),
                                // Forms\Components\TextInput::make('slug')
                                //     ->label('Przyjazny link')
                                //     ->required()
                                //     ->maxLength(2048),
                                Forms\Components\RichEditor::make('body')
                                    ->label('Treść')
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Toggle::make('active')
                                    ->label('Aktywny')
                                    ->required(),
                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Data publikacji')
                                    ->native(false)
                                    ->displayFormat('d.m.Y')
                                    ->closeOnDateSelection()
                                    ->timezone('Europe/Warsaw')
                                    ->default(now())
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
                        // Card::make()
                        //     ->schema([
                        //         Select::make('photos')
                        //             ->multiple()
                        //             ->required()
                        //             ->label('Pinned photos')
                        //             ->nullable()
                        //             ->placeholder('Select photos')
                        //             ->searchable()
                        //             ->preload()
                        //             ->reactive()
                        //             ->relationship('photos', 'image_name'),
                        //     ]),
                    ])->columnSpan(8),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Tytuł')
                    ->limit(35)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        
                        if (strlen($state) <= 35) {
                            return null;
                        }
                        
                        return $state;
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('active')
                    ->label('Aktywny')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Data publikacji')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->description('Aktualne rekrutacje dostępne pod /oferty-pracy')
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
            'index' => Pages\ListJobOffers::route('/'),
            'create' => Pages\CreateJobOffers::route('/create'),
            'view' => Pages\ViewJobOffers::route('/{record}'),
            'edit' => Pages\EditJobOffers::route('/{record}/edit'),
        ];
    }
}
