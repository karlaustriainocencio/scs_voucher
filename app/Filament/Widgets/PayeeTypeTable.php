<?php

namespace App\Filament\Widgets;

use App\Models\Claim;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PayeeTypeTable extends BaseWidget
{
    protected static ?string $heading = 'Claims by Payee Type';
    protected static ?int $sort = 3;

    public function getTableRecordKey($record): string
    {
        return $record->id ?? (string) $record->payee_type;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Claim::query()
                    ->selectRaw('
                        CASE 
                            WHEN payee_type = "App\\Models\\Employee" THEN "Employee"
                            WHEN payee_type = "App\\Models\\Vendor" THEN "Vendor"
                            WHEN payee_type = "App\\Models\\Supplier" THEN "Supplier"
                            ELSE "Unknown"
                        END as payee_type,
                        COUNT(*) as total_claims,
                        SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_claims,
                        SUM(total_amount) as total_amount,
                        AVG(total_amount) as avg_amount,
                        payee_type as id
                    ')
                    ->whereNotNull('payee_type')
                    ->groupBy('payee_type')
                    ->orderBy('total_claims', 'desc')
            )
            ->heading('Claims by Payee Type')
            ->description('Detailed breakdown of claims by payee type')
            ->columns([
                Tables\Columns\TextColumn::make('payee_type')
                    ->label('Payee Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Employee' => 'info',
                        'Vendor' => 'warning',
                        'Supplier' => 'success',
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
