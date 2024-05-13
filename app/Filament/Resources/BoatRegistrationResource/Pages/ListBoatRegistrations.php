<?php

namespace App\Filament\Resources\BoatRegistrationResource\Pages;

use App\Filament\Resources\BoatRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBoatRegistrations extends ListRecords
{
    protected static string $resource = BoatRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
