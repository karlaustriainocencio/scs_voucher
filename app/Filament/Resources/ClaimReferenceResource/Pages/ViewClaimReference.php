<?php

namespace App\Filament\Resources\ClaimReferenceResource\Pages;

use App\Filament\Resources\ClaimReferenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewClaimReference extends ViewRecord
{
    protected static string $resource = ClaimReferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
