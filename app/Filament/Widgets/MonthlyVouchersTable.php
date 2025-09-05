<?php

namespace App\Filament\Widgets;

use App\Models\Voucher;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class MonthlyVouchersTable extends BaseWidget
{
    protected static ?string $heading = 'Monthly Vouchers Generated';
    protected static ?int $sort = 6;

    public function getTableRecordKey($record): string
    {
        return $record->id ?? (string) $record->month_key;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Voucher::query()
                    ->join('claims', 'vouchers.claim_id', '=', 'claims.claim_id')
                    ->selectRaw('
                        DATE_FORMAT(vouchers.created_at, "%Y-%m") as month_key,
                        DATE_FORMAT(vouchers.created_at, "%b %Y") as month,
                        COUNT(vouchers.voucher_id) as vouchers_generated,
                        SUM(claims.total_amount) as total_amount,
                        AVG(claims.total_amount) as avg_amount,
                        DATE_FORMAT(vouchers.created_at, "%Y-%m") as id
                    ')
                    ->where('vouchers.created_at', '>=', Carbon::now()->subMonths(12))
                    ->groupBy('month_key', 'month')
                    ->orderBy('month_key', 'desc')
            )
            ->heading('Monthly Vouchers Generated')
            ->description('Voucher generation and amounts for the last 12 months')
            ->columns([
                Tables\Columns\TextColumn::make('month')
                    ->label('Month')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('vouchers_generated')
                    ->label('Vouchers Generated')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money('SGD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('avg_amount')
                    ->label('Average Amount')
                    ->money('SGD')
                    ->sortable(),
            ])
            ->defaultSort('month_key', 'desc')
            ->paginated(false);
    }
}
