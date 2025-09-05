<?php

namespace App\Filament\Widgets;

use App\Models\Voucher;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class MonthlyVouchersChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Vouchers Generated';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        // Get data for the last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $voucherCount = Voucher::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $data[] = $voucherCount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Vouchers Generated',
                    'data' => $data,
                    'backgroundColor' => 'rgba(168, 85, 247, 0.8)',
                    'borderColor' => 'rgb(168, 85, 247)',
                    'borderWidth' => 2,
                    'fill' => true,
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
                    'text' => 'Voucher Generation Trends',
                ],
            ],
        ];
    }
}
