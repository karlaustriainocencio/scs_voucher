<?php

namespace App\Filament\Resources\ClaimReferenceResource\Widgets;

use App\Models\Claim;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class MonthlyClaimsChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Claims Overview';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $months = collect();
        $approvedData = collect();
        $submittedData = collect();
        $draftData = collect();
        $rejectedData = collect();

        // Get data for the last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');
            
            $months->push($monthName);
            
            $approvedData->push(
                Claim::where('status', 'approved')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count()
            );
            
            $submittedData->push(
                Claim::where('status', 'submitted')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count()
            );
            
            $draftData->push(
                Claim::where('status', 'draft')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count()
            );
            
            $rejectedData->push(
                Claim::where('status', 'rejected')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count()
            );
        }

        return [
            'datasets' => [
                [
                    'label' => 'Approved',
                    'data' => $approvedData->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => '#10b981',
                    'fill' => false,
                ],
                [
                    'label' => 'Submitted',
                    'data' => $submittedData->toArray(),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => '#f59e0b',
                    'fill' => false,
                ],
                [
                    'label' => 'Draft',
                    'data' => $draftData->toArray(),
                    'borderColor' => '#6b7280',
                    'backgroundColor' => '#6b7280',
                    'fill' => false,
                ],
                [
                    'label' => 'Rejected',
                    'data' => $rejectedData->toArray(),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => '#ef4444',
                    'fill' => false,
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
