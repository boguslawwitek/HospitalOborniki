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

class Contact extends Page implements HasForms
{
    use InteractsWithForms;
    
    public static function canAccess(): bool
    {
        return auth()->user()->can('manage_contact') || auth()->user()->can('admin');
    }

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationGroup = 'Ustawienia';
    protected static ?string $title = 'Kontakt';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.custom-edit-page';

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FourExtraLarge;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $firstRecord = \App\Models\Contact::first();

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
                    RichEditor::make('address')
                        ->label('Adres')
                        ->required()
                        ->columnSpanFull(),
                    TextInput::make('telephone')
                        ->label('Telefon')
                        ->required()
                        ->maxLength(2048),
                    TextInput::make('email')
                        ->label('E-mail')
                        ->required()
                        ->maxLength(2048),
                    TextInput::make('fax')
                        ->label('Faks')
                        ->required()
                        ->maxLength(2048),
                ]),
            Card::make()
                ->schema([
                    TextInput::make('system_email')
                        ->label('Na ten adres będzie wysyłany e-mail z formularza kontaktowego')
                        ->required()
                        ->maxLength(2048),
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
            $contact = \App\Models\Contact::first();
            $contact->fill($validatedData);
            $contact->save();

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