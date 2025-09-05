<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

class VoucherController extends Controller
{
    public function generatePDF($voucherId)
    {
        try {
            $voucher = Voucher::with(['claim.claimReferences.category', 'modeOfPayment', 'approvedBy', 'createdBy'])->findOrFail($voucherId);
            $claim = $voucher->claim;
            $claimReferences = $claim->claimReferences()->with('category')->where('rejected', false)->get();
            
            // Improved data cleaning function
            $cleanData = function($text) {
                if (empty($text)) return '';
                
                // Convert to string if it's not already
                $text = (string) $text;
                
                // Remove any null bytes and other problematic characters
                $text = str_replace(["\0", "\r", "\n"], '', $text);
                
                // Ensure proper UTF-8 encoding
                if (!mb_check_encoding($text, 'UTF-8')) {
                    $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
                }
                
                // Remove any invalid UTF-8 characters
                $text = iconv('UTF-8', 'UTF-8//IGNORE', $text);
                
                // Convert to HTML entities for safety
                return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            };
            
            // Build HTML content
            $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher - ' . $cleanData($voucher->voucher_number) . '</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .voucher-container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 0;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 16px;
        }
        .details-section {
            width: 100%;
            margin-bottom: 20px;
        }
        .details-left {
            float: left;
            width: 48%;
        }
        .details-right {
            float: right;
            width: 48%;
        }
        .details-section:after {
            content: "";
            display: table;
            clear: both;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
        }
        .details-table td {
            padding: 8px;
        }
        .details-table td:first-child {
            font-weight: bold;
            width: 120px;
        }
        .claim-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .claim-table th,
        .claim-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .claim-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .claim-table td:last-child {
            text-align: right;
        }
        .total-amount {
            text-align: right;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            flex: 1;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 150px;
            margin: 0 auto;
            padding-top: 10px;
        }
        .signature-name {
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="voucher-container">
        <div class="header">
            <h1>PAYMENT VOUCHER</h1>
            <h2 style="margin: 5px 0; font-size: 12px; color: #666;">' . ($voucher->company === 'CIS' ? 'CIS Certification Pte Ltd' : 'SOCOTEC Certification Singapore Pte Ltd') . '</h2>
        </div>

        <div class="details-section">
            <div class="details-left">
                <table class="details-table">
                    <tr><td>Voucher:</td><td>' . $cleanData($voucher->voucher_number) . '</td></tr>
                    <tr><td>Mode of Payment:</td><td>' . $cleanData($voucher->modeOfPayment->name ?? 'N/A') . '</td></tr>
                </table>
            </div>
            <div class="details-right">
                <table class="details-table">
                    <tr><td>Reference:</td><td>' . $cleanData($claim->reference_number) . '</td></tr>
                    <tr><td>Date:</td><td>' . $cleanData(now()->format('F j, Y')) . '</td></tr>
                </table>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr><td style="padding: 8px; font-weight: bold; width: 120px;">Payee:</td><td style="padding: 8px;">' . $cleanData($this->getPayeeName($claim)) . '</td></tr>
            </table>
            <table class="claim-table">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">S/N</th>
                        <th style="width: 100px; text-align: center;">Category</th>
                        <th style="text-align: center;">Description</th>
                        <th style="width: 100px; text-align: right;">Amount (SGD)</th>
                    </tr>
                </thead>
                <tbody>';
            
            $totalAmount = 0;
            $serialNumber = 1;
            foreach ($claimReferences as $item) {
                $html .= '<tr>
    <td style="width: 50px;">' . $serialNumber . '</td>
    <td style="width: 100px;">' . $cleanData($item->category->name ?? 'N/A') . '</td>
    <td>' . $cleanData($item->description) . '</td>
    <td style="width: 100px;">SGD ' . number_format($item->amount, 2) . '</td>
</tr>';
                $totalAmount += $item->amount;
                $serialNumber++;
            }

            // Add amount in words and total amount in one row
            $html .= '<tr style="border-top: 2px solid #333; background-color: #f5f5f5;">
    <td colspan="3" style="padding: 10px; font-style: italic; text-align: left;">Singapore Dollars: ' . $cleanData($this->amountToWords($totalAmount)) . '</td>
    <td style="padding: 10px; font-size: 8px; text-align: left;">Total Amount: SGD <strong style="float: right;">' . number_format($totalAmount, 2) . '</strong></td>
</tr>';

            $html .= '</tbody>
        </table>
    </div>';

            $html .= '<div class="footer">
            <table style="width: 100%; margin-top: 30px; padding-top: 20px;">
                <tr>
                    <td style="width: 50%; text-align: center; vertical-align: top;">
                        <div style="border-top: 1px solid #333; width: 300px; margin: 0 auto; padding-top: 10px;">
                            <strong>Approved By</strong><br>
                           
                        </div>
                    </td>
                    <td style="width: 50%; text-align: center; vertical-align: top;">
                        <div style="border-top: 1px solid #333; width: 300px; margin: 0 auto; padding-top: 10px;">
                            <strong>Accepted By</strong><br>
                            
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>';

            // Generate PDF using DomPDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('A4');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => false,
                'isRemoteEnabled' => false,
                'defaultFont' => 'Arial',
                'chroot' => public_path(),
                'defaultMediaType' => 'screen',
                'isFontSubsettingEnabled' => false,
                'debugKeepTemp' => false,
                'debugCss' => false,
                'debugLayout' => false,
                'defaultPaperSize' => 'a4',
                'dpi' => 96,
                'fontHeightRatio' => 0.9,
            ]);
            
            $pdfOutput = $pdf->output();
            
            return response($pdfOutput)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="voucher-' . $cleanData($voucher->voucher_number) . '.pdf"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
                
        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage());
            Log::error('Voucher ID: ' . $voucherId);
            Log::error('Error trace: ' . $e->getTraceAsString());
            
            return response('PDF generation failed: ' . $e->getMessage())
                ->header('Content-Type', 'text/plain')
                ->setStatusCode(500);
        }
    }
    
    private function getPayeeName($claim)
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

    private function numberToWords($number)
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
            $words .= $this->numberToWords($billions) . ' Billion ';
            $number %= 1000000000;
        }
        
        // Handle millions
        if ($number >= 1000000) {
            $millions = intval($number / 1000000);
            $words .= $this->numberToWords($millions) . ' Million ';
            $number %= 1000000;
        }
        
        // Handle thousands
        if ($number >= 1000) {
            $thousands = intval($number / 1000);
            $words .= $this->numberToWords($thousands) . ' Thousand ';
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

    private function amountToWords($amount)
    {
        // Split the amount into dollars and cents
        $dollars = intval($amount);
        $cents = round(($amount - $dollars) * 100);
        
        $words = '';
        
        if ($dollars > 0) {
            $words .= $this->numberToWords($dollars);
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
            $words .= $this->numberToWords($cents);
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
