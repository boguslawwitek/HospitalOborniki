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
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Enums\MaxWidth;

class Header extends Page implements HasForms
{
    use InteractsWithForms;
    
    public static function canAccess(): bool
    {
        return auth()->user()->can('manage_header') || auth()->user()->can('admin');
    }

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationGroup = 'Ustawienia';
    protected static ?string $title = 'Nagłówek';
    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.custom-edit-page';

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FourExtraLarge;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $firstRecord = \App\Models\Header::first();

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
                    Grid::make(2)
                        ->schema([
                            TextInput::make('title1')
                                ->label('Tytuł 1')
                                ->required()
                                ->maxLength(2048),
                            TextInput::make('title2')
                                ->label('Tytuł 2')
                                ->required()
                                ->maxLength(2048),
                        ]),
                    TextInput::make('subtitle')
                        ->label('Podtytuł')
                        ->maxLength(2048),
                    FileUpload::make('logo')
                        ->image()
                        ->imageEditor()
                        ->acceptedFileTypes(['image/png'])
                        ->label('Logo strony')
                        ->disk('public')
                        ->directory('header-logo')
                        ->default(null),
                    TextInput::make('telephone')
                        ->label('Numer telefonu')
                        ->required()
                        ->maxLength(15),
                ]),
            Card::make()
                ->schema([
                    Builder::make('links')
                        ->label('Odnośniki')
                        ->addActionLabel('Dodaj odnośnik')
                        ->blocks([
                            Builder\Block::make('link')
                                ->label(function (?array $state): string {
                                    if ($state === null) {
                                        return 'Odnośnik';
                                    }
                            
                                    return $state['name'] ?? 'Odnośnik';
                                })
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Nazwa')
                                        ->required(),
                                    FileUpload::make('icon')
                                        ->image()
                                        ->imageEditor()
                                        ->label('Ikona')
                                        ->disk('public')
                                        ->directory('header-links')
                                        ->default(null),
                                    TextInput::make('icon-alt')
                                        ->label('Opis ikony')
                                        ->default(null),
                                    TextInput::make('url')
                                        ->label('Ścieżka lub URL')
                                        ->required(),
                                    Toggle::make('external')
                                        ->label('Otwieraj w nowej karcie')
                                        ->required(),
                                ])
                                ->columns(1),
                        ])
                ])
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
            $header = \App\Models\Header::first();
            $header->fill($validatedData);
            $header->save();

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