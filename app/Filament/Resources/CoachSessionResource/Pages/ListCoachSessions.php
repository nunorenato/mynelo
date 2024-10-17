<?php

namespace App\Filament\Resources\CoachSessionResource\Pages;

use App\Filament\Resources\CoachSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCoachSessions extends ListRecords
{
    protected static string $resource = CoachSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
