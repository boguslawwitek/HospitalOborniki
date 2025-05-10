<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TagsColumn;

class UserResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()->can('manage_users') || auth()->user()->can('admin');
    }
    
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Ustawienia';
    protected static ?string $modelLabel = 'Administrator';
    protected static ?string $pluralModelLabel = 'Administratorzy';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dane użytkownika')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nazwa użytkownika')
                            ->required(),
        
                        TextInput::make('email')
                            ->label('Adres e-mail')
                            ->email()
                            ->required(),
        
                        TextInput::make('password')
                            ->label('Hasło')
                            ->password()
                            ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => bcrypt($state)),
                        
                        Select::make('roles')
                            ->label('Role')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Użytkownik')->searchable(),
                TextColumn::make('email')->label('E-mail')->searchable(),
                TagsColumn::make('roles.name')->label('Role'),
                TextColumn::make('created_at')->label('Data utworzenia')->dateTime(),
            ])
            ->description('Dodawaj lub edytuj administratorów.')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
