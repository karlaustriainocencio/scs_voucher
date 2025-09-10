<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Add the user's role to the form data for editing
        if ($this->record->user) {
            $data['role'] = $this->record->user->roles->first()?->name ?? 'employee';
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle role update
        if (isset($data['role']) && $this->record->user) {
            $newRole = $data['role'];
            unset($data['role']); // Remove role from employee data

            // Update user's role
            $this->record->user->syncRoles([$newRole]);
        }

        return $data;
    }
}
