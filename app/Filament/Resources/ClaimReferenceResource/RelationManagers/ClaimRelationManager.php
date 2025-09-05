<?php

namespace App\Filament\Resources\ClaimReferenceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ClaimRelationManager extends RelationManager
{
    protected static string $relationship = 'claimReferences';

    protected static ?string $recordTitleAttribute = 'description';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->options(\App\Models\Category::pluck('name', 'category_id'))
                    ->required()
                    ->label('Category'),
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255)
                    ->label('Description'),
                Forms\Components\DatePicker::make('expense_date')
                    ->required()
                    ->label('Expense Date'),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->default('0.00')
                    ->label('Amount'),
                Forms\Components\FileUpload::make('receipt_path')
                    ->label('Receipt')
                    ->directory('receipts')
                    ->required(false)
                    ->maxSize(2048),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->modifyQueryUsing(fn ($query) => $query->with(['category']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\IconColumn::make('rejected')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(function ($record) {
                        if (!$record) return true; // Default to approved if no record
                        return !($record->rejected ?? false);
                    })
                    ->tooltip(function ($record) {
                        if (!$record) return 'Approved';
                        return ($record->rejected ?? false) ? 'Rejected' : 'Approved';
                    }),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        if (!$record || !$record->category) return 'N/A';
                        return $record->category->name ?? 'N/A';
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        if (!$record) return 'N/A';
                        return $record->description ?? 'N/A';
                    }),
                Tables\Columns\TextColumn::make('expense_date')
                    ->date()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        if (!$record) return null;
                        return $record->expense_date ?? null;
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->money('SGD')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        if (!$record) return 0;
                        return $record->amount ?? 0;
                    }),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Rejection Reason')
                    ->limit(50)
                    ->visible(function ($record) {
                        if (!$record) return false;
                        return $record->rejected ?? false;
                    })
                    ->color('danger'),
                Tables\Columns\TextColumn::make('receipt_path')
                    ->label('Receipt')
                    ->formatStateUsing(function ($state) {
                        return $state ? 'Uploaded' : 'No file';
                    })
                    ->badge()
                    ->color(function ($state) {
                        return $state ? 'success' : 'gray';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('rejected')
                    ->label('Rejection Status')
                    ->placeholder('All items')
                    ->trueLabel('Rejected items')
                    ->falseLabel('Approved items'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
