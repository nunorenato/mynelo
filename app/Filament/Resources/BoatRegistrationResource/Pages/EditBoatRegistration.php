<?php

namespace App\Filament\Resources\BoatRegistrationResource\Pages;

use App\Filament\Resources\BoatRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBoatRegistration extends EditRecord
{
    protected static string $resource = BoatRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
