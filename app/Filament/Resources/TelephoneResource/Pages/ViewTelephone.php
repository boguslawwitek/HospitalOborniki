<?php

namespace App\Filament\Resources\TelephoneResource\Pages;

use App\Filament\Resources\TelephoneResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTelephone extends ViewRecord
{
    protected static string $resource = TelephoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
