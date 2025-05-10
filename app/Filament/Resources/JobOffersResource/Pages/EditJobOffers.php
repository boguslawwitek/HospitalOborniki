<?php

namespace App\Filament\Resources\JobOffersResource\Pages;

use App\Filament\Resources\JobOffersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJobOffers extends EditRecord
{
    protected static string $resource = JobOffersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
