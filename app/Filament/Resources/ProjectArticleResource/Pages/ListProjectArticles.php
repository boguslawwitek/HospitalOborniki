<?php

namespace App\Filament\Resources\ProjectArticleResource\Pages;

use App\Filament\Resources\ProjectArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjectArticles extends ListRecords
{
    protected static string $resource = ProjectArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
