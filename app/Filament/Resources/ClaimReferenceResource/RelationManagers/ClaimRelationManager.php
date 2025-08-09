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
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expense_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('receipt_path')
                    ->label('Receipt')
                    ->formatStateUsing(fn ($state) => $state ? 'Uploaded' : 'No file')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
