<?php

namespace App\Filament\Resources\ClaimResource\Pages;

use App\Filament\Resources\ClaimResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClaims extends ListRecords
{
    protected static string $resource = ClaimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('new_claim')
                ->label('New Claim')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(\App\Filament\Resources\ClaimReferenceResource::getUrl('create'))
                ->openUrlInNewTab(false),
        ];
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getTableQuery()->with('claimReferences');
    }
}
