<?php

namespace App\Filament\Resources\MembershipResource\Pages;

use App\Filament\Resources\MembershipResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMembership extends EditRecord
{
    protected static string $resource = MembershipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
