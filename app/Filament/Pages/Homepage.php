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
use Filament\Forms\Components\FileUpload;

class Homepage extends Page implements HasForms
{
    use InteractsWithForms;
    
    public static function canAccess(): bool
    {
        return auth()->user()->can('manage_homepage') || auth()->user()->can('admin');
    }

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationGroup = 'Treści';
    protected static ?string $title = 'Strona główna';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.custom-edit-page';

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FourExtraLarge;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $firstRecord = \App\Models\Homepage::first();

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
                    TextInput::make('title')
                        ->label('Tytuł strony')
                        ->required()
                        ->maxLength(2048),
                    FileUpload::make('photo')
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('homepage')
                        ->label('Zdjęcie'),
                    RichEditor::make('content')
                        ->label('Treść strony')
                        ->required()
                        ->columnSpanFull(),
                ]),
            Card::make()
                ->schema([
                    Builder::make('carousel_photos')
                        ->label('Zdjęcia w karuzeli')
                        ->addActionLabel('Dodaj zdjęcie')
                        ->collapsible()
                        ->collapsed()
                        ->reorderable()
                        ->blocks([
                            Builder\Block::make('carousel_photo')
                                ->label(function (?array $state): string {
                                    if ($state === null) {
                                        return 'Zdjęcie';
                                    }
                            
                                    return $state['name'] ?? 'Zdjęcie';
                                })
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Nazwa')
                                        ->required(),
                                    FileUpload::make('photo')
                                        ->image()
                                        ->imageEditor()
                                        ->required()
                                        ->label('Zdjęcie')
                                        ->disk('public')
                                        ->directory('homepage-carousel')
                                        ->default(null),
                                    TextInput::make('description')
                                        ->label('Opis')
                                        ->default(null)
                                        ->required(),
                                    TextInput::make('url')
                                        ->label('Ścieżka lub URL')
                                        ->required(),
                                    Toggle::make('external')
                                        ->label('Otwieraj w nowej karcie')
                                        ->default(false)
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
            $homepage = \App\Models\Homepage::first();
            $homepage->fill($validatedData);
            $homepage->save();

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