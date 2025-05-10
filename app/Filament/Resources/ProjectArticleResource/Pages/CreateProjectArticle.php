<?php

namespace App\Filament\Resources\ProjectArticleResource\Pages;

use App\Filament\Resources\ProjectArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectArticle extends CreateRecord
{
    protected static string $resource = ProjectArticleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
