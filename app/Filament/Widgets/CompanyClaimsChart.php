<?php

namespace App\Filament\Widgets;

use App\Models\Claim;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class CompanyClaimsChart extends ChartWidget
{
    protected static ?string $heading = 'Claims by Company (Monthly)';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        // Get data for the last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $scs = Claim::where('company', 'SCS')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $cis = Claim::where('company', 'CIS')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $data['scs'][] = $scs;
            $data['cis'][] = $cis;
        }

        return [
            'datasets' => [
                [
                    'label' => 'SCS',
                    'data' => $data['scs'],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'CIS',
                    'data' => $data['cis'],
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'SCS vs CIS Claims Over Time',
                ],
            ],
        ];
    }
}
