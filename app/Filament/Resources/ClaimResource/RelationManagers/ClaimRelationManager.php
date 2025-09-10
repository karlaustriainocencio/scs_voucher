<?php

namespace App\Filament\Resources\ClaimResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClaimRelationManager extends RelationManager
{
    protected static string $relationship = 'claimReferences';

    protected static ?string $recordTitleAttribute = 'description';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Claim Items';
    }

    public static function getModelLabel(): string
    {
        return 'Claim Item';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Claim Items';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Claim Item Details')
                    ->description('Add a new item to this claim')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->label('Category')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(1),
                                Forms\Components\DatePicker::make('expense_date')
                                    ->label('Expense Date')
                                    ->required()
                                    ->default(now())
                                    ->columnSpan(1),
                            ]),
                        Forms\Components\TextInput::make('description')
                            ->label('Description')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter item description'),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('amount')
                                    ->label('Amount')
                                    ->required()
                                    ->numeric()
                                    ->prefix('SGD $')
                                    ->placeholder('0.00')
                                    ->columnSpan(1),
                                Forms\Components\Select::make('rejected')
                                    ->label('Status')
                                    ->options([
                                        null => 'Pending Review',
                                        0 => 'Approved',
                                        1 => 'Rejected',
                                    ])
                                    ->default(null)
                                    ->required()
                                    ->columnSpan(1),
                            ]),
                        Forms\Components\FileUpload::make('receipt_path')
                            ->label('Receipt')
                            ->directory('receipts')
                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                            ->maxSize(5120) // 5MB
                            ->helperText('Upload receipt image or PDF (max 5MB)'),
                    ])
                    ->columns(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('claim_reference_id')
                    ->label('Ref #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('expense_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('SGD')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('rejected')
                    ->label('Status')
                    ->formatStateUsing(fn ($state): string => match($state) {
                        null => 'Pending Review',
                        0 => 'Approved',
                        1 => 'Rejected',
                        default => 'Pending Review'
                    })
                    ->badge()
                    ->color(fn ($state): string => match($state) {
                        null => 'warning',
                        0 => 'success',
                        1 => 'danger',
                        default => 'warning'
                    }),
            ])
            ->defaultSort('claim_reference_id', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('rejected')
                    ->label('Status')
                    ->options([
                        null => 'Pending Review',
                        0 => 'Approved',
                        1 => 'Rejected',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Item')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->modalHeading('Add New Claim Item')
                    ->modalDescription('Add a new item to this claim')
                    ->modalSubmitActionLabel('Add Item'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->color('gray')
                    ->modalHeading('Edit Claim Item')
                    ->modalSubmitActionLabel('Update Item'),
                Tables\Actions\DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ])
                ->bulkActions([
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\BulkAction::make('approve')
                            ->label('Approve Selected')
                            ->icon('heroicon-o-check-circle')
                            ->color('success')
                            ->action(function ($records) {
                                $records->each(function ($record) {
                                    $record->update(['rejected' => 0]);
                                });
                            })
                            ->requiresConfirmation()
                            ->modalHeading('Approve Selected Items')
                            ->modalDescription('Are you sure you want to approve the selected items?')
                            ->modalSubmitActionLabel('Approve Items'),
                        Tables\Actions\BulkAction::make('reject')
                            ->label('Reject Selected')
                            ->icon('heroicon-o-x-circle')
                            ->color('danger')
                            ->action(function ($records) {
                                $records->each(function ($record) {
                                    $record->update(['rejected' => 1]);
                                });
                            })
                            ->requiresConfirmation()
                            ->modalHeading('Reject Selected Items')
                            ->modalDescription('Are you sure you want to reject the selected items?')
                            ->modalSubmitActionLabel('Reject Items'),
                        Tables\Actions\DeleteBulkAction::make()
                            ->label('Delete Selected')
                            ->icon('heroicon-o-trash')
                            ->color('danger'),
                    ]),
                ])
                ->modifyQueryUsing(function ($query) {
                    return $query->with(['category']);
                });
    }
}
