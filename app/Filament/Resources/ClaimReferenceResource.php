<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClaimReferenceResource\Pages;
use App\Filament\Resources\ClaimReferenceResource\RelationManagers;
use App\Models\Claim;
use App\Models\ClaimReference;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;

class ClaimReferenceResource extends Resource
{
    protected static ?string $model = Claim::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationLabel = 'Claim References';
    
    protected static ?string $modelLabel = 'Claim Reference';
    
    protected static ?string $pluralModelLabel = 'Claim References';

    public static function shouldRegisterNavigation(): bool
    {
        return false; // Hide from navigation since it's accessed through Claims
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Company selection - only in create form
                Forms\Components\Select::make('company')
                    ->options([
                        'SCS' => 'SCS',
                        'CIS' => 'CIS',
                    ])
                    ->default(session('selected_company', 'SCS'))
                    ->required()
                    ->label('Company')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Update reference number when company changes
                        if ($state) {
                            $newReferenceNumber = \App\Models\Claim::generateReferenceNumber($state);
                            $set('reference_number', $newReferenceNumber);
                        }
                    })
                    ->live()
                    ->visible(fn ($livewire) => $livewire instanceof Pages\CreateClaimReference),
                
                // Reference number - auto-generated based on company
                Forms\Components\TextInput::make('reference_number')
                    ->required()
                    ->label('Claim / Reference no.')
                    ->maxLength(255)
                    ->default(function ($get) {
                        $company = $get('company') ?? session('selected_company', 'SCS');
                        return \App\Models\Claim::generateReferenceNumber($company);
                    })
                    ->disabled()
                    ->dehydrated()
                    ->live()
                    ->helperText('Reference number is automatically generated based on company')
                    ->visible(fn ($livewire) => $livewire instanceof Pages\CreateClaimReference),
                
                // Payee fields - available in both create and edit forms
                Select::make('payee_type')
                    ->options([
                        'App\Models\Employee' => 'Employee',
                        'App\Models\Vendor' => 'Vendor',
                        'App\Models\Supplier' => 'Supplier',
                    ])
                    ->reactive()
                    ->required()
                    ->helperText('Claims will be automatically created for Vendors and Suppliers'),
                
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
                
               
                
                // Claim items repeater - available in both create and edit
                Forms\Components\Repeater::make('claim_items')
                    ->label('Claim Items')
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
                        Forms\Components\Toggle::make('rejected')
                            ->label('Reject this item')
                            ->default(false)
                            ->helperText('Mark this specific item as rejected'),
                        Forms\Components\Textarea::make('reason')
                            ->label('Rejection Reason')
                            ->rows(2)
                            ->maxLength(500)
                            ->placeholder('Enter reason for rejecting this item')
                            ->helperText('Required if this item is rejected')
                            ->visible(fn (callable $get) => $get('rejected'))
                            ->required(fn (callable $get) => $get('rejected')),
                    ])
                    ->defaultItems(1)
                    ->minItems(1)
                    ->maxItems(50)
                    ->addActionLabel('Add Item')
                    ->reorderable(false)
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => 
                        ($state['description'] ?? 'Item') . 
                        (isset($state['rejected']) && $state['rejected'] ? ' ❌' : '')
                    )
                    ->columnSpanFull(),
                
                     // Status - available in both create and edit forms
                Forms\Components\Select::make('status')
                ->options([
                    'draft' => 'Draft',
                    'submitted' => 'Submitted',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])
                ->required()
                ->default('draft'),
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
                    })
                    ->visible(fn ($record) => $record && $record->payee_type !== null),
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

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'submitted' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        // Only show the relation manager on view pages, not on create/edit pages
        // where the repeater form handles the items
        if (request()->routeIs('*.edit') || request()->routeIs('*.create')) {
            return [];
        }
        
        return [
            RelationManagers\ClaimRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClaimReferences::route('/'),
            'create' => Pages\CreateClaimReference::route('/create'),
            'edit' => Pages\EditClaimReference::route('/{record}/edit'),
            'view' => Pages\ViewClaimReference::route('/{record}'),
        ];
    }


}
