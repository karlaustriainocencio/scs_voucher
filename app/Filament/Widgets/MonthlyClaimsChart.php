<?php

namespace App\Filament\Widgets;

use App\Models\Claim;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class MonthlyClaimsChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Claims Overview';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        // Get data for the last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $approved = Claim::where('status', 'approved')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $submitted = Claim::where('status', 'submitted')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $draft = Claim::where('status', 'draft')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $rejected = Claim::where('status', 'rejected')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $data['approved'][] = $approved;
            $data['submitted'][] = $submitted;
            $data['draft'][] = $draft;
            $data['rejected'][] = $rejected;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Approved',
                    'data' => $data['approved'],
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Submitted',
                    'data' => $data['submitted'],
                    'backgroundColor' => 'rgba(245, 158, 11, 0.8)',
                    'borderColor' => 'rgb(245, 158, 11)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Draft',
                    'data' => $data['draft'],
                    'backgroundColor' => 'rgba(107, 114, 128, 0.8)',
                    'borderColor' => 'rgb(107, 114, 128)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Rejected',
                    'data' => $data['rejected'],
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
                    'text' => 'Claims by Status Over Time',
                ],
            ],
        ];
    }
}
