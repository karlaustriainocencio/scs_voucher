<?php

namespace App\Filament\Resources\ClaimReferenceResource\Pages;

use App\Filament\Resources\ClaimReferenceResource;
use App\Models\Claim;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class EditClaimReference extends EditRecord
{
    protected static string $resource = ClaimReferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $claimItems = $data['claim_items'] ?? [];
        $totalAmount = 0;

        // Calculate total amount from all items
        foreach ($claimItems as $item) {
            $totalAmount += (float) ($item['amount'] ?? 0);
        }

        // Update the claim
        $record->update([
            'payee_type' => $data['payee_type'],
            'payee_id' => $data['payee_id'],
            'total_amount' => $totalAmount,
            'status' => $data['status'] ?? 'draft',
        ]);

        // Delete existing claim references
        $record->claimReferences()->delete();

        // Create new claim references
        foreach ($claimItems as $item) {
            if (!empty($item['category_id']) && !empty($item['description'])) {
                \App\Models\ClaimReference::create([
                    'claim_id' => $record->claim_id,
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
            ->title('Claim updated')
            ->body("Claim {$record->reference_number} has been updated with " . count($claimItems) . " items. Total amount: SGD " . number_format($totalAmount, 2))
            ->success()
            ->send();

        return $record;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load existing claim references as claim_items
        $claim = $this->getRecord();
        $claimReferences = $claim->claimReferences()->get();
        
        if ($claimReferences->count() > 0) {
            $data['claim_items'] = $claimReferences->map(function ($reference) {
                return [
                    'category_id' => $reference->category_id,
                    'description' => $reference->description,
                    'expense_date' => $reference->expense_date,
                    'amount' => $reference->amount,
                    'receipt_path' => $reference->receipt_path,
                ];
            })->toArray();
        } else {
            // If no claim references exist, provide a default empty item
            $data['claim_items'] = [
                [
                    'category_id' => null,
                    'description' => '',
                    'expense_date' => null,
                    'amount' => '0.00',
                    'receipt_path' => null,
                ]
            ];
        }

        return $data;
    }
}
