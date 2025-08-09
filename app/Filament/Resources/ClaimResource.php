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
                Forms\Components\TextInput::make('total_amount')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('draft')
                    ->required()
                    ->reactive()
                    ->disabled(fn ($livewire) => $livewire instanceof \App\Filament\Resources\ClaimResource\Pages\CreateClaim)
                    ->helperText(fn ($livewire) => $livewire instanceof \App\Filament\Resources\ClaimResource\Pages\CreateClaim 
                        ? 'Status will be set to Draft for new claims. You can change it after creation.' 
                        : ''),
                Forms\Components\DateTimePicker::make('submitted_at')
                    ->disabled(fn ($livewire) => $livewire instanceof \App\Filament\Resources\ClaimResource\Pages\CreateClaim)
                    ->default(fn (callable $get) => $get('status') === 'submitted' ? now() : null)
                    ->helperText(fn ($livewire) => $livewire instanceof \App\Filament\Resources\ClaimResource\Pages\CreateClaim 
                        ? 'This will be automatically filled when status changes to Submitted.' 
                        : ''),
                Forms\Components\TextInput::make('reviewed_by')
                    ->numeric()
                    ->disabled(fn ($livewire) => $livewire instanceof \App\Filament\Resources\ClaimResource\Pages\CreateClaim)
                    ->helperText(fn ($livewire) => $livewire instanceof \App\Filament\Resources\ClaimResource\Pages\CreateClaim 
                        ? 'This field is only available when editing claims.' 
                        : ''),
                Forms\Components\DateTimePicker::make('reviewed_at')
                    ->disabled(fn ($livewire) => $livewire instanceof \App\Filament\Resources\ClaimResource\Pages\CreateClaim)
                    ->helperText(fn ($livewire) => $livewire instanceof \App\Filament\Resources\ClaimResource\Pages\CreateClaim 
                        ? 'This field is only available when editing claims.' 
                        : ''),
                Forms\Components\Textarea::make('rejection_reason')
                    ->columnSpanFull()
                    ->disabled(fn ($livewire) => $livewire instanceof \App\Filament\Resources\ClaimResource\Pages\CreateClaim)
                    ->helperText(fn ($livewire) => $livewire instanceof \App\Filament\Resources\ClaimResource\Pages\CreateClaim 
                        ? 'This field is only available when editing claims.' 
                        : ''),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['employee', 'payee']))
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('employee.first_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payee_type')
                    ->label('Payee Type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payee_name')
                    ->label('Payee Name')
                    ->getStateUsing(function ($record) {
                        if (!$record->payee_type || !$record->payee_id) {
                            return 'N/A';
                        }
                        
                        try {
                            $model = new $record->payee_type();
                            $payee = $record->payee_type::find($record->payee_id);
                            
                            if (!$payee) {
                                return 'N/A';
                            }
                            
                            if ($record->payee_type === 'App\Models\Employee') {
                                return $payee->first_name . ' ' . $payee->last_name;
                            } else {
                                return $payee->name;
                            }
                        } catch (\Exception $e) {
                            return 'N/A';
                        }
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'submitted' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviewed_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->dateTime()
                    ->sortable(),
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
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
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
