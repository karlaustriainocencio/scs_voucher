<?php

namespace App\Filament\Widgets;

use App\Models\Voucher;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class VoucherStatsWidget extends BaseWidget
{

    protected function getStats(): array
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $currentYear = Carbon::now()->year;

        // Total vouchers
        $totalVouchers = Voucher::count();
        $totalAmount = Voucher::with('claim')->get()->sum('claim.total_amount');

        // This month
        $thisMonthVouchers = Voucher::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
        $thisMonthAmount = Voucher::with('claim')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->get()
            ->sum('claim.total_amount');

        // This year
        $thisYearVouchers = Voucher::whereYear('created_at', $currentYear)->count();
        $thisYearAmount = Voucher::with('claim')
            ->whereYear('created_at', $currentYear)
            ->get()
            ->sum('claim.total_amount');

        return [
            Stat::make('Total Vouchers', $totalVouchers)
                ->description('SGD ' . number_format($totalAmount, 2))
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),
            Stat::make('This Month', $thisMonthVouchers)
                ->description('SGD ' . number_format($thisMonthAmount, 2))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
            Stat::make('This Year', $thisYearVouchers)
                ->description('SGD ' . number_format($thisYearAmount, 2))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),
        ];
    }
}
