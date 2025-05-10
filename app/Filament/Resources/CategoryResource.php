<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use App\Models\Article;

class CategoryResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()->can('manage_categories') || auth()->user()->can('admin');
    }
    
    protected static ?string $model = Category::class;
    protected static ?string $navigationGroup = 'Treści';
    protected static ?string $navigationIcon = 'heroicon-o-hashtag';
    protected static ?string $modelLabel = 'Kategoria';
    protected static ?string $pluralModelLabel = 'Kategorie';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Tytuł')
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
                Repeater::make('articles')
                    ->relationship('articles')
                    ->schema([
                        Select::make('id')
                            ->label('Artykuł / Odnośnik')
                            ->options(Article::all()->pluck('title', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->disabled(true)
                            ->default(null),
                    ])
                    ->orderColumn('sort_order')
                    ->reorderableWithButtons()
                    ->label('Artykuły w tej kategorii')
                    ->visibleOn('edit')
                    ->hidden(fn ($get) => empty($get('articles')))
                    ->defaultItems(0)
                    ->collapsible(false)
                    ->deletable(false)
                    ->addable(false)
                    ->addActionLabel('Dodaj artykuł / odnośnik')
                    ->itemLabel(fn (array $state): ?string => $state['id'] ? Article::find($state['id'])?->title : null)
                    ->afterStateUpdated(function ($state, $record) {
                        if (!$record) return;
                        
                        $state = collect($state)->values()->all();
                        
                        foreach ($state as $index => $item) {
                            if (isset($item['id'])) {
                                \DB::table('category_article')
                                    ->where('category_id', $record->id)
                                    ->where('article_id', $item['id'])
                                    ->update(['sort_order' => $index]);
                            }
                        }
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Tytuł')
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Data aktualizacji')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->description('W tym miejscu możesz dodawać oraz usuwać kategorie, w trakcie edycjii możliwa jest zmiana kolejności artykułów.')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCategories::route('/'),
        ];
    }
}
