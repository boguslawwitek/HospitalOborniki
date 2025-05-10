<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DietResource\Pages;
use App\Filament\Resources\DietResource\RelationManagers;
use App\Models\Diet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Card;
class DietResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()->can('manage_diets') || auth()->user()->can('admin');
    }
    
    protected static ?string $model = Diet::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Pilotaż "Dobry posiłek"';
    protected static ?string $modelLabel = 'Dieta';
    protected static ?string $pluralModelLabel = 'Diety';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Data diety')
                            ->native(false)
                            ->displayFormat('d.m.Y')
                            ->closeOnDateSelection()
                            ->timezone('Europe/Warsaw')
                            ->default(now())
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nazwa')
                            ->required()
                            ->maxLength(2048),
                        Forms\Components\FileUpload::make('breakfast_photo')
                            ->image()
                            ->imageEditor()
                            ->label('Zdjęcie śniadania')
                            ->disk('public')
                            ->directory('diets-photos')
                            ->default(null),
                        Forms\Components\RichEditor::make('breakfast_body')
                            ->label('Treść śniadania')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('lunch_photo')
                            ->image()
                            ->imageEditor()
                            ->label('Zdjęcie obiadu')
                            ->disk('public')
                            ->directory('diets-photos')
                            ->default(null),
                        Forms\Components\RichEditor::make('lunch_body')
                            ->label('Treść obiadu')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('diet_attachment')
                            ->label('Załącznik')
                            ->disk('public')
                            ->directory('diets-attachments')
                            ->default(null),
                        Forms\Components\Toggle::make('active')
                            ->label('Aktywny')
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nazwa')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('breakfast_photo')
                    ->label('Zdjęcie śniadania')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('lunch_photo')
                    ->label('Zdjęcie obiadu')
                    ->searchable(),
                Tables\Columns\IconColumn::make('active')
                    ->label('Aktywny')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Data diety')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->defaultPaginationPageOption(20)
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
            'index' => Pages\ListDiets::route('/'),
            'create' => Pages\CreateDiet::route('/create'),
            'view' => Pages\ViewDiet::route('/{record}'),
            'edit' => Pages\EditDiet::route('/{record}/edit'),
        ];
    }
}
