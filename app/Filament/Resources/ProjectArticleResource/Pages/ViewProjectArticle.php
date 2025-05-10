<?php

namespace App\Filament\Resources\ProjectArticleResource\Pages;

use App\Filament\Resources\ProjectArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProjectArticle extends ViewRecord
{
    protected static string $resource = ProjectArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
