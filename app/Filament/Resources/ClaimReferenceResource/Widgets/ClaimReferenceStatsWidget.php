<?php

namespace App\Filament\Resources\ClaimReferenceResource\Widgets;

use App\Models\Claim;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClaimReferenceStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalAmount = Claim::sum('total_amount');
        $draftCount = Claim::where('status', 'draft')->count();
        $submittedCount = Claim::where('status', 'submitted')->count();
        $approvedCount = Claim::where('status', 'approved')->count();
        $rejectedCount = Claim::where('status', 'rejected')->count();

        return [
            Stat::make('Total Amount', '₱' . number_format($totalAmount, 2))
                ->description('All claims')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
            Stat::make('Draft Claims', $draftCount)
                ->description('Claims in draft status')
                ->descriptionIcon('heroicon-m-document')
                ->color('gray'),
            Stat::make('Submitted Claims', $submittedCount)
                ->description('Claims submitted for review')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Approved Claims', $approvedCount)
                ->description('Claims approved')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Rejected Claims', $rejectedCount)
                ->description('Claims rejected')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
