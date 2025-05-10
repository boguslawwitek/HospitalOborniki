<?php

namespace App\Filament\Resources\ProjectArticleResource\Pages;

use App\Filament\Resources\ProjectArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProjectArticle extends EditRecord
{
    protected static string $resource = ProjectArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
