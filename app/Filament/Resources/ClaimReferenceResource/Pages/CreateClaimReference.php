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
        $payeeType = $data['payee_type'] ?? null;
        $payeeId = $data['payee_id'] ?? null;
        $claimItems = $data['claim_items'] ?? [];
        
        $totalAmount = 0;

        // Calculate total amount from all items
        foreach ($claimItems as $item) {
            $totalAmount += (float) ($item['amount'] ?? 0);
        }

        // Create a new claim (reference number will be auto-generated)
        $claim = Claim::create([
            'payee_type' => $payeeType,
            'payee_id' => $payeeId,
            'total_amount' => $totalAmount,
            'status' => $data['status'] ?? 'draft',
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
                ]);
            }
        }

        // Show notification
        Notification::make()
            ->title('New claim created')
            ->body("A new claim with reference number {$claim->reference_number} has been created with " . count($claimItems) . " items. Total amount: SGD " . number_format($totalAmount, 2))
            ->success()
            ->send();

        return $claim;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
