<?php

namespace App\Filament\Widgets;

use App\Models\Claim;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PayeeTypeClaimsChart extends ChartWidget
{
    protected static ?string $heading = 'Claims by Payee Type (Monthly)';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        // Get data for the last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $employee = Claim::where('payee_type', 'App\Models\Employee')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $vendor = Claim::where('payee_type', 'App\Models\Vendor')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $supplier = Claim::where('payee_type', 'App\Models\Supplier')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $data['employee'][] = $employee;
            $data['vendor'][] = $vendor;
            $data['supplier'][] = $supplier;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Employee',
                    'data' => $data['employee'],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Vendor',
                    'data' => $data['vendor'],
                    'backgroundColor' => 'rgba(245, 158, 11, 0.8)',
                    'borderColor' => 'rgb(245, 158, 11)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Supplier',
                    'data' => $data['supplier'],
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
                    'text' => 'Claims by Payee Type Over Time',
                ],
            ],
        ];
    }
}
