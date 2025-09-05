<?php

namespace App\Filament\Widgets;

use App\Models\Claim;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class MonthlyClaimsTable extends BaseWidget
{
    protected static ?string $heading = 'Monthly Claims Overview';
    protected static ?int $sort = 2;

    public function getTableRecordKey($record): string
    {
        return $record->id ?? (string) $record->month_key;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Claim::query()
                    ->selectRaw('
                        DATE_FORMAT(created_at, "%Y-%m") as month_key,
                        DATE_FORMAT(created_at, "%b %Y") as month,
                        SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                        SUM(CASE WHEN status = "submitted" THEN 1 ELSE 0 END) as submitted,
                        SUM(CASE WHEN status = "draft" THEN 1 ELSE 0 END) as draft,
                        SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                        COUNT(*) as total,
                        DATE_FORMAT(created_at, "%Y-%m") as id
                    ')
                    ->where('created_at', '>=', Carbon::now()->subMonths(12))
                    ->groupBy('month_key', 'month')
                    ->orderBy('month_key', 'desc')
            )
            ->heading('Monthly Claims Overview')
            ->description('Claims breakdown by status for the last 12 months')
            ->columns([
                Tables\Columns\TextColumn::make('month')
                    ->label('Month')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('approved')
                    ->label('Approved')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('submitted')
                    ->label('Submitted')
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('draft')
                    ->label('Draft')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('rejected')
                    ->label('Rejected')
                    ->badge()
                    ->color('danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
            ])
            ->defaultSort('month_key', 'desc')
            ->paginated(false);
    }
}
