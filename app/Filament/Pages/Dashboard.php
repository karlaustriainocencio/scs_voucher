<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Session;
use App\Filament\Widgets\VoucherStatsWidget;
use App\Filament\Widgets\MonthlyClaimsChart;
use App\Filament\Widgets\PayeeTypeClaimsChart;
use App\Filament\Widgets\CompanyClaimsChart;
use App\Filament\Widgets\MonthlyVouchersChart;
use App\Filament\Widgets\MonthlyClaimsTable;
use App\Filament\Widgets\PayeeTypeTable;
use App\Filament\Widgets\CompanyStatsTable;
use App\Filament\Widgets\MonthlyVouchersTable;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getHeaderActions(): array
    {
        return [
            Action::make('switchCompany')
                ->label('Switch Company')
                ->icon('heroicon-o-building-office')
                ->form([
                    Select::make('company')
                        ->label('Select Company')
                        ->options([
                            'CIS' => 'CIS',
                            'SCS' => 'SCS',
                        ])
                        ->default(session('selected_company', 'SCS'))
                        ->required(),
                ])
                ->action(function (array $data): void {
                    Session::put('selected_company', $data['company']);
                    $this->redirect(request()->header('Referer'));
                })
                ->color('primary'),
        ];
    }

    public function getWidgets(): array
    {
        return [
            VoucherStatsWidget::class,
            MonthlyClaimsChart::class,
            PayeeTypeClaimsChart::class,
            CompanyClaimsChart::class,
            MonthlyVouchersChart::class,
            MonthlyClaimsTable::class,
            PayeeTypeTable::class,
            CompanyStatsTable::class,
            MonthlyVouchersTable::class,
        ];
    }
}
