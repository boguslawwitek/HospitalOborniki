<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NavigationItemResource\Pages;
use App\Models\NavigationItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use App\Models\Article;
use App\Models\Category;
use Filament\Tables\Actions\Action;

class NavigationItemResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()->can('manage_navigation') || auth()->user()->can('admin');
    }
    
    protected static ?string $model = NavigationItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    protected static ?string $navigationGroup = 'Ogólne';
    protected static ?string $modelLabel = 'Nawigacja';
    protected static ?string $pluralModelLabel = 'Nawigacja';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nazwa (Opcjonalnie)')
                    ->maxLength(255)
                    ->default(null),
                Select::make('navigable_type')
                    ->label('Wybierz typ')
                    ->default('Article')
                    ->options([
                        'Article' => 'Artykuł / Odnośnik',
                        'Category' => 'Kategoria',
                    ])
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        $set('navigable_id', null);
                    })
                    ->required(),
                    Select::make('navigable_id')
                    ->label('Wybierz element')
                    ->options(function ($get) {
                        if ($get('navigable_type') === 'Article') {
                            return Article::all()->mapWithKeys(function ($article) {
                                if ($article->type === 'link') {
                                    return [$article->id => 'Odnośnik: ' . $article->title];
                                } else {
                                    return [$article->id => 'Artykuł: ' . $article->title];
                                }
                            })->toArray();
                        } elseif ($get('navigable_type') === 'Category') {
                            return Category::all()->pluck('title', 'id')->mapWithKeys(function ($title, $id) {
                                return [$id => 'Kategoria: ' . $title];
                            })->toArray();
                        }
                
                        return [];
                    })
                    ->reactive()
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->getStateUsing(function ($record) {
                        if($record->name) return $record->name;

                        $type = $record->navigable_type;
                        $id = $record->navigable_id;

                        return match ($type) {
                            'Article' => Article::class::find($id)?->title ?? 'Artykuł / Odnośnik bez tytułu',
                            'Category' => Category::class::find($id)?->title ?? 'Kategoria bez tytułu',
                            default => $record->name,
                        };
                    })
                    ->label('Nazwa')
                    ->searchable(),
                TextColumn::make('navigable_type')
                    ->label('Typ')
                    ->formatStateUsing(function ($record, $state) {
                        if($state === 'Article') {
                            $id = $record->navigable_id;
                            $type = Article::class::find($id)?->type;

                            if($type === 'link') {
                                return 'Odnośnik';
                            } else if($type === 'article') {
                                return 'Artykuł';
                            }
                        } else {
                            return 'Kategoria';
                        }
                    })
                    ->searchable(),
            ])
            ->description('W tym miejscu możesz dodać Artykuł, Odnośnik lub Kategorię do nawigacji, możesz ustalać własną kolejność oraz nadawać niestandardowe nazwy.')
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
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
            'index' => Pages\ListNavigationItems::route('/'),
            'create' => Pages\CreateNavigationItem::route('/create'),
            'view' => Pages\ViewNavigationItem::route('/{record}'),
            'edit' => Pages\EditNavigationItem::route('/{record}/edit'),
        ];
    }
}
