<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Card;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;

class Location extends Page implements HasForms
{
    use InteractsWithForms;
    
    public static function canAccess(): bool
    {
        return auth()->user()->can('manage_location') || auth()->user()->can('admin');
    }

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Pozostałe';
    protected static ?string $title = 'Mapa budynków';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.custom-edit-page';

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FourExtraLarge;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $firstRecord = \App\Models\Location::first();

        if ($firstRecord) {
            $this->data = $firstRecord->toArray();
            $this->form->fill($this->data);
        }
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Card::make()
                ->schema([
                    Select::make('photo_id')
                        ->label('Powiązane zdjęcie')
                        ->options(function () {
                            return \App\Models\Photo::all()->pluck('image_name', 'id');
                        })
                        ->nullable()
                        ->default(null)
                        ->searchable()
                        ->preload()
                        ->reactive(),
                ]),
        ])->statePath('data');
    }

    public function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Zapisz')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $validatedData = $this->form->getState();

        try {
            $location = \App\Models\Location::first();
            $location->fill($validatedData);
            $location->save();

            Notification::make()
                ->title('Sukces!')
                ->body('Rekord został zapisany pomyślnie.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Błąd!')
                ->body('Wystąpił problem podczas zapisywania rekordu: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}