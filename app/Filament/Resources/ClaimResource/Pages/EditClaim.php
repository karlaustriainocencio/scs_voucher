<?php

namespace App\Filament\Resources\ClaimResource\Pages;

use App\Filament\Resources\ClaimResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditClaim extends EditRecord
{
    protected static string $resource = ClaimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Check if status was changed to approved
        if ($this->record->wasChanged('status') && $this->record->status === 'approved') {
            // Show notification
            Notification::make()
                ->title('Claim Approved')
                ->body('All claim references have been automatically approved.')
                ->success()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
