<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class EmployeeResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()->can('manage_employees') || auth()->user()->can('admin');
    }
    
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'Pozostałe';
    protected static ?string $modelLabel = 'Pracownicy';
    protected static ?string $pluralModelLabel = 'Pracownicy';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('section')
                            ->label('Sekcja')
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
                Card::make()
                    ->schema([
                        Builder::make('employees')
                            ->label('Pracownicy')
                            ->blocks([
                                Builder\Block::make('employee')
                                    ->label('Pracownik')
                                    ->schema([
                                        TextInput::make('other-section')
                                            ->label('Inna sekcja'),
                                        TextInput::make('title')
                                            ->label('Stopień naukowy'),
                                        TextInput::make('first_name')
                                            ->label('Imię')
                                            ->required(),
                                        TextInput::make('last_name')
                                            ->label('Nazwisko')
                                            ->required(),
                                        TextInput::make('position')
                                            ->label('Stanowisko')
                                            ->required(),
                                        TextInput::make('email')
                                            ->label('E-mail'),
                                        TextInput::make('phone')
                                            ->label('Telefon'),
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
            ->description('W tym miejscu możesz dodać, edytować oraz usuwać sekcje z administracją. Po utworzeniu sekcji pamiętaj o dodaniu jej do nawigacji jako odnośnika. Każda sekcja dostępna jest pod linkiem /administracja/slug')
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
