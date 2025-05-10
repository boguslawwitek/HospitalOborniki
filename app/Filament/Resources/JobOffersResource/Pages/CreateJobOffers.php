<?php

namespace App\Filament\Resources\JobOffersResource\Pages;

use App\Filament\Resources\JobOffersResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJobOffers extends CreateRecord
{
    protected static string $resource = JobOffersResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['slug'] = '/empty';
        return $data;
    }
}
