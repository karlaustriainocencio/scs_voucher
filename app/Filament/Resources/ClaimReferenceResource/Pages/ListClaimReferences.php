<?php

namespace App\Filament\Resources\ClaimReferenceResource\Pages;

use App\Filament\Resources\ClaimReferenceResource;
use App\Models\Claim;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListClaimReferences extends ListRecords
{
    protected static string $resource = ClaimReferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('viewClaimDetails')
                ->label('View Claim Details')
                ->icon('heroicon-o-eye')
                ->url(fn () => route('filament.admin.resources.claims.index'))
                ->openUrlInNewTab(),
        ];
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getTableQuery()->with('claimReferences');
    }

}
