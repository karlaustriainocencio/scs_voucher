<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Claim;
use App\Models\ClaimReference;
use App\Models\Voucher;
use App\Models\Employee;
use App\Models\Vendor;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\ModeOfPayment;
use App\Models\User;
use Carbon\Carbon;

class ClaimsAndVouchersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing data
        $employees = Employee::all();
        $vendors = Vendor::all();
        $suppliers = Supplier::all();
        $categories = Category::all();
        $modeOfPayments = ModeOfPayment::all();
        $users = User::all();

        if ($employees->isEmpty() || $categories->isEmpty() || $modeOfPayments->isEmpty()) {
            $this->command->error('Please run the basic seeders first (EmployeeSeeder, CategorySeeder, etc.)');
            return;
        }

        $companies = ['SCS', 'CIS'];
        $statuses = ['draft', 'submitted', 'approved', 'rejected'];
        $payeeTypes = ['App\Models\Employee', 'App\Models\Vendor', 'App\Models\Supplier'];

        // Sample expense descriptions
        $expenseDescriptions = [
            'Business lunch with client',
            'Taxi fare to client meeting',
            'Office supplies purchase',
            'Hotel accommodation for business trip',
            'Internet and phone expenses',
            'Fuel for company vehicle',
            'Printing and stationery',
            'Coffee and refreshments for meeting',
            'Parking fees',
            'Conference registration fee',
            'Equipment rental',
            'Cleaning services',
            'Security services',
            'Maintenance and repairs',
            'Software license renewal',
        ];

        // Sample rejection reasons for individual claim references
        $rejectionReasons = [
            'Insufficient documentation provided',
            'Expense not within company policy',
            'Receipt is unclear or missing',
            'Amount exceeds approved limit',
            'Expense not business related',
            'Duplicate expense submitted',
            'Missing approval from manager',
            'Receipt date is outside claim period',
            'Personal expense, not business related',
            'Receipt amount does not match claimed amount',
        ];

        // Create 15 claims with various scenarios
        for ($i = 1; $i <= 15; $i++) {
            $company = $companies[array_rand($companies)];
            $status = $statuses[array_rand($statuses)];
            $payeeType = $payeeTypes[array_rand($payeeTypes)];
            
            // Select appropriate payee based on type
            $payeeId = null;
            switch ($payeeType) {
                case 'App\Models\Employee':
                    $payeeId = $employees->random()->employee_id;
                    break;
                case 'App\Models\Vendor':
                    $payeeId = $vendors->random()->vendor_id;
                    break;
                case 'App\Models\Supplier':
                    $payeeId = $suppliers->random()->supplier_id;
                    break;
            }

            // Generate unique reference number
            $yearMonth = now()->format('ym');
            $prefix = $company . $yearMonth;
            $existingCount = Claim::where('company', $company)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();
            $nextNumber = $existingCount + $i + 1000; // Add offset to avoid conflicts
            $referenceNumber = $prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // Create claim
            $claim = Claim::create([
                'reference_number' => $referenceNumber,
                'payee_type' => $payeeType,
                'payee_id' => $payeeId,
                'total_amount' => 0, // Will be calculated after claim references
                'status' => $status,
                'submitted_at' => $status !== 'draft' ? Carbon::now()->subDays(rand(1, 30)) : null,
                'reviewed_by' => $status === 'approved' || $status === 'rejected' ? $users->random()->user_id : null,
                'reviewed_at' => $status === 'approved' || $status === 'rejected' ? Carbon::now()->subDays(rand(1, 15)) : null,
                'rejection_reason' => $status === 'rejected' ? $rejectionReasons[array_rand($rejectionReasons)] : null,
                'company' => $company,
                'created_at' => Carbon::now()->subDays(rand(1, 60)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);

            // Create 1-4 claim references for each claim
            $numReferences = rand(1, 4);
            $totalAmount = 0;
            $rejectedCount = 0;

            for ($j = 1; $j <= $numReferences; $j++) {
                $amount = rand(10, 500) + (rand(0, 99) / 100);
                $expenseDate = Carbon::now()->subDays(rand(1, 90));
                
                // Individual claim reference rejection logic
                $rejected = rand(1, 10) <= 3; // 30% chance of being rejected
                if ($rejected) {
                    $rejectedCount++;
                }
                
                $claimReference = ClaimReference::create([
                    'claim_id' => $claim->claim_id,
                    'category_id' => $categories->random()->category_id,
                    'description' => $expenseDescriptions[array_rand($expenseDescriptions)],
                    'expense_date' => $expenseDate,
                    'amount' => $amount,
                    'receipt_path' => rand(1, 10) <= 8 ? 'receipts/sample_receipt_' . rand(1, 5) . '.pdf' : null, // 80% have receipts
                    'rejected' => $rejected,
                    'reason' => $rejected ? $rejectionReasons[array_rand($rejectionReasons)] : null,
                    'created_at' => $expenseDate,
                    'updated_at' => $expenseDate,
                ]);

                // Only add to total if not rejected
                if (!$rejected) {
                    $totalAmount += $amount;
                }
            }

            // Update claim total amount (only non-rejected items)
            $claim->update(['total_amount' => $totalAmount]);

            // Create voucher for approved claims (70% of approved claims get vouchers)
            // Note: A claim can be approved even if some claim references are rejected
            if ($status === 'approved' && rand(1, 10) <= 7) {
                $paymentDate = Carbon::now()->subDays(rand(1, 10));
                
                // Generate unique voucher number
                $voucherPrefix = 'V' . now()->format('ym');
                $voucherCount = Voucher::whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month)
                    ->count();
                $voucherNumber = $voucherPrefix . '-' . str_pad($voucherCount + $i + 1000, 3, '0', STR_PAD_LEFT);
                
                Voucher::create([
                    'claim_id' => $claim->claim_id,
                    'voucher_number' => $voucherNumber,
                    'mode_of_payment_id' => $modeOfPayments->random()->mode_of_payment_id,
                    'payment_date' => $paymentDate,
                    'remarks' => $rejectedCount > 0 
                        ? "Payment processed for approved items. {$rejectedCount} items rejected."
                        : 'Payment processed successfully for all items',
                    'approved_by' => $users->random()->user_id,
                    'created_by' => $users->random()->user_id,
                    'company' => $company,
                    'created_at' => $paymentDate,
                    'updated_at' => $paymentDate,
                ]);
            }
        }

        $this->command->info('Successfully created 15 claims with claim references and vouchers!');
        $this->command->info('- Claims created: 15');
        $this->command->info('- Claim references created: ' . ClaimReference::count());
        $this->command->info('- Vouchers created: ' . Voucher::count());
        $this->command->info('- Rejected claim references: ' . ClaimReference::where('rejected', true)->count());
    }
}
