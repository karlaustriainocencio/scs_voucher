<?php

namespace App\Filament\Widgets;

use App\Models\Claim;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class CompanyStatsTable extends BaseWidget
{
    protected static ?string $heading = 'Claims by Company';
    protected static ?int $sort = 4;

    public function getTableRecordKey($record): string
    {
        return $record->id ?? (string) $record->company;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Claim::query()
                    ->selectRaw('
                        company,
                        COUNT(*) as total_claims,
                        SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_claims,
                        SUM(CASE WHEN YEAR(created_at) = ? AND MONTH(created_at) = ? THEN 1 ELSE 0 END) as this_month,
                        SUM(CASE WHEN YEAR(created_at) = ? THEN 1 ELSE 0 END) as this_year,
                        SUM(total_amount) as total_amount,
                        AVG(total_amount) as avg_amount,
                        company as id
                    ', [
                        Carbon::now()->year,
                        Carbon::now()->month,
                        Carbon::now()->year
                    ])
                    ->whereNotNull('company')
                    ->groupBy('company')
                    ->orderBy('total_claims', 'desc')
            )
            ->heading('Claims by Company')
            ->description('Detailed breakdown of claims by company (SCS vs CIS)')
            ->columns([
                Tables\Columns\TextColumn::make('company')
                    ->label('Company')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SCS' => 'info',
                        'CIS' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_claims')
                    ->label('Total Claims')
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_claims')
                    ->label('Approved Claims')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('this_month')
                    ->label('This Month')
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('this_year')
                    ->label('This Year')
                    ->badge()
                    ->color('info')
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
            ->defaultSort('total_claims', 'desc')
            ->paginated(false);
    }
}
