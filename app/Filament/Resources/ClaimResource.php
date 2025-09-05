<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClaimResource\Pages;
use App\Filament\Resources\ClaimResource\RelationManagers;
use App\Models\Claim;
use App\Filament\Resources\ClaimResource\Pages\CreateClaim;
use App\Models\Employee;
use App\Models\Vendor;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClaimResource extends Resource
{
    protected static ?string $model = Claim::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('reference_number')
                    ->required()
                    ->maxLength(255)
                    ->default(fn () => \App\Models\Claim::generateReferenceNumber())
                    ->disabled()
                    ->dehydrated()
                    ->helperText('Reference number is automatically generated'),
                Forms\Components\Select::make('company')
                    ->options([
                        'CIS' => 'CIS',
                        'SCS' => 'SCS',
                    ])
                    ->default(session('selected_company', 'SCS'))
                    ->required()
                    ->label('Company'),
                Select::make('payee_type')
                    ->options([
                        'App\Models\Employee' => 'Employee',
                        'App\Models\Vendor' => 'Vendor',
                        'App\Models\Supplier' => 'Supplier',
                    ])
                    ->reactive()
                    ->required(),
                Select::make('payee_id')
                    ->label('Payee')
                    ->required()
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search, $state, callable $get) {
                        $payeeType = $get('payee_type');
                        
                        if (!$payeeType) {
                            return [];
                        }
                        
                        try {
                            $model = new $payeeType();
                            $primaryKey = $model->getKeyName();
                            
                            if ($payeeType === 'App\Models\Employee') {
                                return $payeeType::where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->get()
                                    ->mapWithKeys(function ($employee) use ($primaryKey) {
                                        return [$employee->$primaryKey => $employee->first_name . ' ' . $employee->last_name];
                                    });
                            } else {
                                return $payeeType::where('name', 'like', "%{$search}%")
                                    ->pluck('name', $primaryKey);
                            }
                        } catch (\Exception $e) {
                            return [];
                        }
                    })
                    ->getOptionLabelUsing(function ($value, $state, callable $get) {
                        $payeeType = $get('payee_type');
                        
                        if (!$payeeType || !$value) {
                            return null;
                        }
                        
                        try {
                            $model = new $payeeType();
                            $primaryKey = $model->getKeyName();
                            
                            $payee = $payeeType::find($value);
                            if (!$payee) {
                                return null;
                            }
                            
                            if ($payeeType === 'App\Models\Employee') {
                                return $payee->first_name . ' ' . $payee->last_name;
                            } else {
                                return $payee->name;
                            }
                        } catch (\Exception $e) {
                            return null;
                        }
                    })
                    ->options(function (callable $get) {
                        $payeeType = $get('payee_type');
                        
                        if (!$payeeType) {
                            return [];
                        }
                        
                        try {
                            $model = new $payeeType();
                            $primaryKey = $model->getKeyName();
                            
                            if ($payeeType === 'App\Models\Employee') {
                                return $payeeType::get()
                                    ->mapWithKeys(function ($employee) use ($primaryKey) {
                                        return [$employee->$primaryKey => $employee->first_name . ' ' . $employee->last_name];
                                    });
                            } else {
                                return $payeeType::pluck('name', $primaryKey);
                            }
                        } catch (\Exception $e) {
                            return [];
                        }
                    })
                    ->placeholder('Select a payee type first')
                    ->helperText('Choose a payee type above, then select the specific payee'),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required()
                    ->default('draft'),
                Forms\Components\TextInput::make('total_amount')
                    ->required()
                    ->numeric()
                    ->default('0.00')
                    ->label('Total Amount'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company')
                    ->label('Company')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'CIS' => 'success',
                        'SCS' => 'info',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Claim Reference')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('payee_type')
                    ->label('Payee Type')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'App\Models\Employee' => 'Employee',
                        'App\Models\Vendor' => 'Vendor',
                        'App\Models\Supplier' => 'Supplier',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'App\Models\Employee' => 'info',
                        'App\Models\Vendor' => 'warning',
                        'App\Models\Supplier' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('SGD')
                    ->sortable()
                    ->label('Total Amount'),
                Tables\Columns\TextColumn::make('test_column')
                ->label('Items Count')
                ->getStateUsing(function ($record) {
                    if (!$record) return 'No record';
                    
                    $totalItems = $record->claimReferences->count();
                    $rejectedItems = $record->claimReferences->where('rejected', true)->count();
                    $approvedItems = $totalItems - $rejectedItems;
                    
                    if ($rejectedItems > 0) {
                        return "❌ {$rejectedItems} rejected | ✅ {$approvedItems} approved | 📊 {$totalItems} total";
                    } else {
                        return "✅ {$totalItems} items (all approved)";
                    }
                })
                ->badge()
                ->color(function ($record) {
                    if (!$record) return 'gray';
                    
                    $rejectedItems = $record->claimReferences->where('rejected', true)->count();
                    return $rejectedItems > 0 ? 'danger' : 'success';
                })
            ->sortable(false),
                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->selectablePlaceholder(false)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')
                    ->options([
                        'CIS' => 'CIS',
                        'SCS' => 'SCS',
                    ])
                    ->label('Company'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('payee_type')
                    ->options([
                        'App\Models\Employee' => 'Employee',
                        'App\Models\Vendor' => 'Vendor',
                        'App\Models\Supplier' => 'Supplier',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'approved']);
                            });
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'rejected']);
                            });
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('change_status')
                        ->label('Change Status')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('New Status')
                                ->options([
                                    'draft' => 'Draft',
                                    'submitted' => 'Submitted',
                                    'approved' => 'Approved',
                                    'rejected' => 'Rejected',
                                ])
                                ->required()
                                ->default('approved'),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update(['status' => $data['status']]);
                            });
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ClaimRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClaims::route('/'),
            'create' => Pages\CreateClaim::route('/create'),
            'edit' => Pages\EditClaim::route('/{record}/edit'),
        ];
    }
} 
