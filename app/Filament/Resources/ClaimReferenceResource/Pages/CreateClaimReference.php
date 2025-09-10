<?php

namespace App\Filament\Resources\ClaimReferenceResource\Pages;

use App\Filament\Resources\ClaimReferenceResource;
use App\Models\Claim;
use App\Models\ClaimReference;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class CreateClaimReference extends CreateRecord
{
    protected static string $resource = ClaimReferenceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $company = $data['company'] ?? 'SCS';
        $payeeType = $data['payee_type'] ?? null;
        $payeeId = $data['payee_id'] ?? null;
        $status = $data['status'] ?? 'draft';
        $claimItems = $data['claim_items'] ?? [];
        
        $totalAmount = 0;

        // Calculate total amount from all non-rejected items
        foreach ($claimItems as $item) {
            if (!isset($item['rejected']) || !$item['rejected']) {
                $totalAmount += (float) ($item['amount'] ?? 0);
            }
        }

        // Create a new claim (reference number will be auto-generated based on company)
        $claim = Claim::create([
            'company' => $company,
            'payee_type' => $payeeType,
            'payee_id' => $payeeId,
            'total_amount' => $totalAmount,
            'status' => $status,
        ]);

        // Create all claim references linked to the new claim
        foreach ($claimItems as $item) {
            if (!empty($item['category_id']) && !empty($item['description'])) {
                \App\Models\ClaimReference::create([
                    'claim_id' => $claim->claim_id,
                    'category_id' => $item['category_id'],
                    'description' => $item['description'],
                    'expense_date' => $item['expense_date'],
                    'amount' => $item['amount'],
                    'receipt_path' => $item['receipt_path'] ?? null,
                    'rejected' => $item['rejected'] ?? false,
                    'reason' => $item['rejected'] ? ($item['reason'] ?? 'No reason provided') : null,
                ]);
            }
        }

        // Show notification
        Notification::make()
            ->title('New Claim Created')
            ->body("Claim {$claim->reference_number} has been created with " . count($claimItems) . " items. Total amount: SGD " . number_format($totalAmount, 2) . ".")
            ->success()
            ->send();

        return $claim;
    }

    protected function getRedirectUrl(): string
    {
        return \App\Filament\Resources\ClaimResource::getUrl('index');
    }

    protected function getRelations(): array
    {
        // Remove the table from create form - creating is done through the repeater above
        return [];
    }

    // Optional: Add method to handle form state changes
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure reference number is generated based on company
        if (isset($data['company']) && !isset($data['reference_number'])) {
            $data['reference_number'] = \App\Models\Claim::generateReferenceNumber($data['company']);
        }
        
        return $data;
    }
}
