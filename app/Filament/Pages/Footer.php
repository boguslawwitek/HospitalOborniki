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
use Filament\Forms\Components\Select;

use Filament\Support\Enums\MaxWidth;

class Footer extends Page implements HasForms
{
    use InteractsWithForms;
    
    public static function canAccess(): bool
    {
        return auth()->user()->can('manage_footer') || auth()->user()->can('admin');
    }

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationGroup = 'Ustawienia';
    protected static ?string $title = 'Stopka';
    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.pages.custom-edit-page';

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FourExtraLarge;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $firstRecord = \App\Models\Footer::first();

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
                    Select::make('wosp_link')
                        ->label('Powiązany artykuł WOŚP')
                        ->options(function () {
                            return \App\Models\Article::all()->pluck('title', 'id');
                        })
                        ->nullable()
                        ->default(null)
                        ->searchable()
                        ->preload()
                        ->reactive(),
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
                                    TextInput::make('url')
                                        ->label('Ścieżka lub URL')
                                        ->required(),
                                    Toggle::make('external')
                                        ->label('Otwieraj w nowej karcie')
                                        ->required(),
                                ])
                                ->columns(2),
                        ])
                    ]),
            Card::make()
                ->schema([
                    Builder::make('registration_hours')
                        ->label('Godziny rejestracji')
                        ->addActionLabel('Dodaj dzień z godzinami')
                        ->blocks([
                            Builder\Block::make('registration_hour')
                                ->label(function (?array $state): string {
                                    if ($state === null) {
                                        return 'Godziny rejestracji';
                                    }
                            
                                    return $state['day'] ?? 'Godzina rejestracji';
                                })
                                ->schema([
                                    TextInput::make('day')
                                        ->label('Dzień tygodnia')
                                        ->required(),
                                    TextInput::make('hours')
                                        ->label('Godziny')
                                        ->required(),
                                ])
                                ->columns(2),
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
            $footer = \App\Models\Footer::first();
            $footer->fill($validatedData);
            $footer->save();

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