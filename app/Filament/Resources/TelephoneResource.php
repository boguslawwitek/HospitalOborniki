<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TelephoneResource\Pages;
use App\Filament\Resources\TelephoneResource\RelationManagers;
use App\Models\Telephone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Card;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Builder;

class TelephoneResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()->can('manage_telephones') || auth()->user()->can('admin');
    }
    
    protected static ?string $model = Telephone::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';
    protected static ?string $navigationGroup = 'Pozostałe';
    protected static ?string $modelLabel = 'Telefony';
    protected static ?string $pluralModelLabel = 'Telefony';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('section')
                            ->label('Sekcja')
                            ->required()
                            ->maxLength(2048),
                    ]),
                Card::make()
                    ->schema([
                        Builder::make('telephones')
                            ->label('Telefony')
                            ->blocks([
                                Builder\Block::make('telephone')
                                    ->label('Telefon')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nazwa')
                                            ->required(),
                                        TextInput::make('number')
                                            ->label('Numer')
                                            ->required(),
                                    ])
                                    ->columns(2),
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('section')
                    ->label('Sekcja')
                    ->searchable(),
            ])
            ->defaultSort('sort_order')
            ->description('W tym miejscu możesz dodać, edytować oraz usuwać sekcje z numerami telefonów.')
            ->reorderable('sort_order')
            ->reorderRecordsTriggerAction(
                fn (Action $action, bool $isReordering) => $action
                    ->button()
                    ->label($isReordering ? 'Wyłącz zmianę kolejności' : 'Zmień kolejność'),
            )
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
            'index' => Pages\ListTelephones::route('/'),
            'create' => Pages\CreateTelephone::route('/create'),
            'view' => Pages\ViewTelephone::route('/{record}'),
            'edit' => Pages\EditTelephone::route('/{record}/edit'),
        ];
    }
}
