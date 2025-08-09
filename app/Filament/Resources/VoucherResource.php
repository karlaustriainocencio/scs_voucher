<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VoucherResource\Pages;
use App\Filament\Resources\VoucherResource\RelationManagers;
use App\Models\Voucher;
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
use Illuminate\Support\Facades\Log;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('voucher_number')
                    ->required()
                    ->maxLength(255)
                    ->default(fn () => \App\Models\Voucher::generateVoucherNumber())
                    ->disabled()
                    ->dehydrated()
                    ->helperText('Voucher number is automatically generated'),
                Forms\Components\Select::make('claim_id')
                    ->label('Claim')
                    ->options(\App\Models\Claim::pluck('reference_number', 'claim_id'))
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $claim = \App\Models\Claim::find($state);
                            if ($claim) {
                                $set('total_amount', $claim->total_amount);
                                
                                // Load claim details
                                $claimReferences = $claim->claimReferences()->with('category')->get();
                                $claimDetails = $claimReferences->map(function ($reference) {
                                    return [
                                        'category_name' => $reference->category->name ?? 'N/A',
                                        'description' => $reference->description,
                                        'expense_date' => $reference->expense_date,
                                        'amount' => $reference->amount,
                                    ];
                                })->toArray();
                                
                                $set('claim_details', $claimDetails);
                            }
                        }
                    })
                    ->afterStateHydrated(function ($state, callable $set) {
                        // This runs when the form is loaded with existing data (edit mode)
                        if ($state) {
                            $claim = \App\Models\Claim::find($state);
                            if ($claim) {
                                $set('total_amount', $claim->total_amount);
                                
                                // Load claim details
                                $claimReferences = $claim->claimReferences()->with('category')->get();
                                $claimDetails = $claimReferences->map(function ($reference) {
                                    return [
                                        'category_name' => $reference->category->name ?? 'N/A',
                                        'description' => $reference->description,
                                        'expense_date' => $reference->expense_date,
                                        'amount' => $reference->amount,
                                    ];
                                })->toArray();
                                
                                $set('claim_details', $claimDetails);
                            }
                        }
                    }),
                Forms\Components\TextInput::make('total_amount')
                    ->label('Claim Total Amount')
                    ->disabled()
                    ->dehydrated(false)
                    ->numeric()
                    ->prefix('SGD '),
                Forms\Components\Section::make('Claim Details')
                    ->schema([
                        Forms\Components\Placeholder::make('claim_items_list')
                            ->label('Claim Items')
                            ->content(function ($get) {
                                $claimDetails = $get('claim_details');
                                if (!$claimDetails || empty($claimDetails)) {
                                    return 'No items found';
                                }
                                
                                $html = '<div class="space-y-2">';
                                foreach ($claimDetails as $index => $item) {
                                    $html .= '<div class="border rounded p-3 bg-gray-50">';
                                    $html .= '<div class="flex justify-between items-start">';
                                    $html .= '<div class="flex-1">';
                                    $html .= '<div class="font-medium text-sm text-gray-900">' . ($item['description'] ?? 'No description') . '</div>';
                                    $html .= '<div class="text-sm text-gray-600">Category: ' . ($item['category_name'] ?? 'N/A') . '</div>';
                                    $html .= '<div class="text-sm text-gray-600">Date: ' . ($item['expense_date'] ?? 'N/A') . '</div>';
                                    $html .= '</div>';
                                    $html .= '<div class="text-right">';
                                    $html .= '<div class="font-semibold text-sm text-gray-900">SGD ' . number_format($item['amount'] ?? 0, 2) . '</div>';
                                    $html .= '</div>';
                                    $html .= '</div>';
                                    $html .= '</div>';
                                }
                                $html .= '</div>';
                                
                                return new \Illuminate\Support\HtmlString($html);
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                Forms\Components\Select::make('mode_of_payment_id')
                    ->relationship('modeOfPayment', 'name'),
                Forms\Components\DatePicker::make('payment_date')
                    ->required(),
                Forms\Components\Textarea::make('remarks')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('voucher_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('claim.reference_number')
                    ->label('Claim Reference')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('claim.total_amount')
                    ->label('Claim Amount')
                    ->money('SGD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('claim_details_count')
                    ->label('Items Count')
                    ->getStateUsing(function ($record) {
                        return $record->claim->claimReferences()->count();
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('modeOfPayment.name')
                    ->label('Mode of Payment')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approvedBy.name')
                    ->label('Approved By')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->searchable()
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('previewVoucher')
                    ->label('Preview Voucher')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalContent(function ($record) {
                        $voucher = $record;
                        $claim = $voucher->claim;
                        $claimReferences = $claim->claimReferences()->with('category')->get();
                        
                        $html = '<div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; border: 2px solid #333; background: white;">';
                        
                        // Header
                        $html .= '<div style="text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px;">';
                        $html .= '<h1 style="margin: 0; color: #333; font-size: 24px;">VOUCHER</h1>';
                        $html .= '</div>';
                        
                        // Voucher Details - Two Column Layout
                        $html .= '<div style="margin-bottom: 20px; display: flex; justify-content: space-between;">';
                        
                        // Left Column
                        $html .= '<div style="width: 48%;">';
                        $html .= '<table style="width: 100%; border-collapse: collapse;">';
                        $html .= '<tr><td style="padding: 8px; font-weight: bold; width: 120px;">Voucher:</td><td style="padding: 8px;">' . $voucher->voucher_number . '</td></tr>';
                        $html .= '<tr><td style="padding: 8px; font-weight: bold;">Mode of Payment:</td><td style="padding: 8px;">' . ($voucher->modeOfPayment->name ?? 'N/A') . '</td></tr>';
                        $html .= '</table>';
                        $html .= '</div>';
                        
                        // Right Column
                        $html .= '<div style="width: 48%;">';
                        $html .= '<table style="width: 100%; border-collapse: collapse;">';
                        $html .= '<tr><td style="padding: 8px; font-weight: bold; width: 120px;">Claim Reference:</td><td style="padding: 8px;">' . $claim->reference_number . '</td></tr>';
                        $html .= '<tr><td style="padding: 8px; font-weight: bold;">Date:</td><td style="padding: 8px;">' . now()->format('F j, Y') . '</td></tr>';
                        $html .= '</table>';
                        $html .= '</div>';
                        $html .= '</div>';
                        
                        // Payee Information
                        $html .= '<div style="margin-bottom: 20px;">';
                        $html .= '<table style="width: 100%; border-collapse: collapse;">';
                        $html .= '<tr><td style="padding: 8px; font-weight: bold; width: 120px;">Payee:</td><td style="padding: 8px;">' . self::getPayeeNameStatic($claim) . '</td></tr>';
                        $html .= '</table>';
                        
                        // Claim Items Table
                        $html .= '<table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd; margin-bottom: 20px;">';
                        $html .= '<thead>';
                        $html .= '<tr>';
                        $html .= '<th style="padding: 10px; border: 1px solid #ddd; text-align: left; background-color: #f5f5f5;">S/N</th>';
                        $html .= '<th style="padding: 10px; border: 1px solid #ddd; text-align: left; background-color: #f5f5f5;">Category</th>';
                        $html .= '<th style="padding: 10px; border: 1px solid #ddd; text-align: left; background-color: #f5f5f5;">Description</th>';
                        $html .= '<th style="padding: 10px; border: 1px solid #ddd; text-align: left; background-color: #f5f5f5;">Amount (SGD)</th>';
                        $html .= '</tr>';
                        $html .= '</thead>';
                        $html .= '<tbody>';
                        
                        $totalAmount = 0;
                        $serialNumber = 1;
                        foreach ($claimReferences as $item) {
                            $html .= '<tr>';
                            $html .= '<td style="padding: 10px; border: 1px solid #ddd;">' . $serialNumber . '</td>';
                            $html .= '<td style="padding: 10px; border: 1px solid #ddd;">' . ($item->category->name ?? 'N/A') . '</td>';
                            $html .= '<td style="padding: 10px; border: 1px solid #ddd;">' . $item->description . '</td>';
                            $html .= '<td style="padding: 10px; border: 1px solid #ddd; text-align: right;">SGD ' . number_format($item->amount, 2) . '</td>';
                            $html .= '</tr>';
                            $totalAmount += $item->amount;
                            $serialNumber++;
                        }
                        
                        // Add amount in words and total amount in one row
                        $html .= '<tr style="border-top: 2px solid #333; background-color: #f5f5f5;">';
                        $html .= '<td colspan="2" style="padding: 10px; font-style: italic; text-align: left;">Singapore Dollars ' . self::amountToWordsStatic($totalAmount) . '</td>';
                        $html .= '<td colspan="2" style="padding: 10px; font-weight: bold; font-size: 16px; text-align: right;">Total Amount: SGD ' . number_format($totalAmount, 2) . '</td>';
                        $html .= '</tr>';
                        
                        $html .= '</tbody>';
                        $html .= '</table>';
                        $html .= '</div>';
                        
                        // Remarks
                        if (!empty($voucher->remarks)) {
                            $html .= '<div style="margin-bottom: 20px;">';
                            $html .= '<h3 style="margin: 0 0 10px 0; color: #333;">Remarks</h3>';
                            $html .= '<p style="margin: 0; padding: 10px; background-color: #f9f9f9; border: 1px solid #ddd;">' . nl2br($voucher->remarks) . '</p>';
                            $html .= '</div>';
                        }
                        
                        // Footer
                        $html .= '<div style="margin-top: 30px; border-top: 2px solid #333; padding-top: 20px;">';
                        $html .= '<table style="width: 100%;">';
                        $html .= '<tr>';
                        $html .= '<td style="width: 50%; text-align: center; vertical-align: top;">';
                        $html .= '<div style="border-top: 1px solid #333; width: 300px; margin: 0 auto; padding-top: 10px;">';
                        $html .= '<strong>Approved By</strong><br>';
                        $html .= '<span style="font-size: 12px; color: #666;">' . ($voucher->approvedBy->name ?? 'N/A') . '</span>';
                        $html .= '</div>';
                        $html .= '</td>';
                        $html .= '<td style="width: 50%; text-align: center; vertical-align: top;">';
                        $html .= '<div style="border-top: 1px solid #333; width: 300px; margin: 0 auto; padding-top: 10px;">';
                        $html .= '<strong>Accepted By</strong><br>';
                        $html .= '<span style="font-size: 12px; color: #666;">' . ($voucher->createdBy->name ?? 'N/A') . '</span>';
                        $html .= '</div>';
                        $html .= '</td>';
                        $html .= '</tr>';
                        $html .= '</table>';
                        $html .= '</div>';
                        
                        $html .= '</div>';
                        
                        return new \Illuminate\Support\HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
                Tables\Actions\Action::make('generatePDF')
                    ->label('Generate PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn ($record) => route('voucher.download-pdf', $record->voucher_id))
                    ->openUrlInNewTab()
                    ->requiresConfirmation()
                    ->modalHeading('Generate PDF')
                    ->modalDescription('Are you sure you want to generate a PDF for this voucher?')
                    ->modalSubmitActionLabel('Generate PDF'),
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
            'index' => Pages\ListVouchers::route('/'),
            'create' => Pages\CreateVoucher::route('/create'),
            'edit' => Pages\EditVoucher::route('/{record}/edit'),
        ];
    }

    public static function getPayeeNameStatic($claim)
    {
        if (!$claim->payee_type || !$claim->payee_id) {
            Log::info('No payee type or ID found for claim: ' . $claim->claim_id);
            return 'N/A';
        }
        
        try {
            // Use the morphTo relationship to get the payee
            $payee = $claim->payee;
            
            if (!$payee) {
                // Fallback: try to get payee directly from the model
                $payeeModel = $claim->payee_type;
                $payee = $payeeModel::find($claim->payee_id);
                
                if (!$payee) {
                    Log::info('Payee not found for claim: ' . $claim->claim_id . ', payee_type: ' . $claim->payee_type . ', payee_id: ' . $claim->payee_id);
                    return 'N/A';
                }
            }
            
            Log::info('Payee found: ' . get_class($payee) . ' with ID: ' . $payee->getKey());
            
            // Handle different payee types
            switch ($claim->payee_type) {
                case 'App\Models\Employee':
                    $name = $payee->first_name . ' ' . $payee->last_name;
                    Log::info('Employee name: ' . $name);
                    return $name;
                case 'App\Models\Vendor':
                case 'App\Models\Supplier':
                    $name = $payee->name;
                    Log::info('Vendor/Supplier name: ' . $name);
                    return $name;
                default:
                    $name = $payee->name ?? 'N/A';
                    Log::info('Default name: ' . $name);
                    return $name;
            }
        } catch (\Exception $e) {
            Log::error('Payee name extraction error: ' . $e->getMessage());
            return 'N/A';
        }
    }

    private static function numberToWordsStatic($number)
    {
        $ones = [
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen'
        ];
        
        $tens = [
            2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
            6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
        ];
        
        $hundreds = [
            1 => 'One Hundred', 2 => 'Two Hundred', 3 => 'Three Hundred', 4 => 'Four Hundred',
            5 => 'Five Hundred', 6 => 'Six Hundred', 7 => 'Seven Hundred', 8 => 'Eight Hundred',
            9 => 'Nine Hundred'
        ];
        
        if ($number == 0) {
            return 'Zero';
        }
        
        $words = '';
        
        // Handle billions
        if ($number >= 1000000000) {
            $billions = intval($number / 1000000000);
            $words .= self::numberToWordsStatic($billions) . ' Billion ';
            $number %= 1000000000;
        }
        
        // Handle millions
        if ($number >= 1000000) {
            $millions = intval($number / 1000000);
            $words .= self::numberToWordsStatic($millions) . ' Million ';
            $number %= 1000000;
        }
        
        // Handle thousands
        if ($number >= 1000) {
            $thousands = intval($number / 1000);
            $words .= self::numberToWordsStatic($thousands) . ' Thousand ';
            $number %= 1000;
        }
        
        // Handle hundreds
        if ($number >= 100) {
            $hundred = intval($number / 100);
            $words .= $hundreds[$hundred] . ' ';
            $number %= 100;
        }
        
        // Handle tens and ones
        if ($number > 0) {
            if ($number < 20) {
                $words .= $ones[$number];
            } else {
                $ten = intval($number / 10);
                $one = $number % 10;
                $words .= $tens[$ten];
                if ($one > 0) {
                    $words .= ' ' . $ones[$one];
                }
            }
        }
        
        return trim($words);
    }

    private static function amountToWordsStatic($amount)
    {
        // Split the amount into dollars and cents
        $dollars = intval($amount);
        $cents = round(($amount - $dollars) * 100);
        
        $words = '';
        
        if ($dollars > 0) {
            $words .= self::numberToWordsStatic($dollars);
            if ($dollars == 1) {
                $words .= ' Dollar';
            } else {
                $words .= ' Dollars';
            }
        }
        
        if ($cents > 0) {
            if ($dollars > 0) {
                $words .= ' and ';
            }
            $words .= self::numberToWordsStatic($cents);
            if ($cents == 1) {
                $words .= ' Cent';
            } else {
                $words .= ' Cents';
            }
        }
        
        if ($dollars == 0 && $cents == 0) {
            $words = 'Zero Dollars';
        }
        
        return $words . ' Only';
    }
}
