<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Vendor;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Claim;
use App\Models\ClaimReference;
use App\Models\Voucher;
use App\Models\ModeOfPayment;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CompleteSampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing company-specific data
        $this->command->info('Clearing existing company data...');
        Claim::whereIn('company', ['CIS', 'SCS'])->delete();
        Voucher::whereIn('company', ['CIS', 'SCS'])->delete();
        Employee::whereIn('company', ['CIS', 'SCS'])->delete();
        Vendor::whereIn('company', ['CIS', 'SCS'])->delete();
        Supplier::whereIn('company', ['CIS', 'SCS'])->delete();

        // Create shared master data
        $this->createMasterData();
        
        // Create CIS Company Data
        $this->createCISData();
        
        // Create SCS Company Data
        $this->createSCSData();
        
        $this->command->info('Complete sample data created successfully!');
    }

    private function createMasterData()
    {
        // Create Categories (shared across companies)
        $categories = [
            'Travel & Transportation' => 'Travel expenses and transportation costs',
            'Office Supplies' => 'Office supplies and equipment',
            'Marketing & Advertising' => 'Marketing and advertising expenses',
            'Training & Development' => 'Training and development costs',
            'Consulting Services' => 'Professional consulting services',
            'IT & Software' => 'IT services and software licenses',
            'Utilities & Services' => 'Utility bills and service charges',
            'Equipment & Maintenance' => 'Equipment purchase and maintenance',
        ];

        foreach ($categories as $name => $description) {
            Category::firstOrCreate(['name' => $name], ['description' => $description]);
        }

        // Create Mode of Payments
        $payments = [
            'Bank Transfer' => 'Direct bank transfer',
            'Cheque' => 'Payment by cheque',
            'Cash' => 'Cash payment',
            'Credit Card' => 'Credit card payment',
        ];

        foreach ($payments as $name => $description) {
            ModeOfPayment::firstOrCreate(['name' => $name], ['description' => $description]);
        }

        // Create Departments
        $departments = [
            'Information Technology' => 'IT Department',
            'Human Resources' => 'HR Department',
            'Finance' => 'Finance Department',
            'Marketing' => 'Marketing Department',
            'Sales' => 'Sales Department',
            'Operations' => 'Operations Department',
        ];

        foreach ($departments as $name => $description) {
            Department::firstOrCreate(['name' => $name], ['description' => $description]);
        }

        // Create Designations
        $designations = [
            'Manager' => 'Manager position',
            'Senior' => 'Senior position',
            'Junior' => 'Junior position',
            'Director' => 'Director position',
            'Associate' => 'Associate position',
        ];

        foreach ($designations as $name => $description) {
            Designation::firstOrCreate(['name' => $name], ['description' => $description]);
        }
    }

    private function createCISData()
    {
        $this->command->info('Creating CIS Company Data...');

        // Create CIS Users
        $cisAdmin = User::firstOrCreate([
            'email' => 'cis.admin@cis.com',
        ], [
            'name' => 'CIS Admin',
            'password' => Hash::make('password'),
        ]);

        $cisApprover = User::firstOrCreate([
            'email' => 'cis.approver@cis.com',
        ], [
            'name' => 'CIS Approver',
            'password' => Hash::make('password'),
        ]);

        // Create CIS Employee Users
        $cisEmployees = [
            ['email' => 'john.smith@cis.com', 'name' => 'John Smith', 'phone' => '91234567'],
            ['email' => 'sarah.johnson@cis.com', 'name' => 'Sarah Johnson', 'phone' => '91234568'],
            ['email' => 'michael.chen@cis.com', 'name' => 'Michael Chen', 'phone' => '91234569'],
            ['email' => 'emily.wong@cis.com', 'name' => 'Emily Wong', 'phone' => '91234570'],
            ['email' => 'alex.tan@cis.com', 'name' => 'Alex Tan', 'phone' => '91234571'],
        ];

        $cisEmployeeUsers = [];
        foreach ($cisEmployees as $employee) {
            $user = User::firstOrCreate([
                'email' => $employee['email'],
            ], [
                'name' => $employee['name'],
                'password' => Hash::make('password'),
            ]);
            $cisEmployeeUsers[] = $user;
        }

        // Get departments and designations
        $departments = Department::all()->keyBy('name');
        $designations = Designation::all()->keyBy('name');

        // Create CIS Employees
        $cisEmployeeRecords = [];
        $cisEmployeeRecords[] = Employee::create([
            'user_id' => $cisEmployeeUsers[0]->user_id,
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john.smith@cis.com',
            'phone_number' => '91234567',
            'address' => '123 Main Street, Singapore 123456',
            'department_id' => $departments['Information Technology']->department_id,
            'designation_id' => $designations['Manager']->designation_id,
            'company' => 'CIS',
        ]);

        $cisEmployeeRecords[] = Employee::create([
            'user_id' => $cisEmployeeUsers[1]->user_id,
            'first_name' => 'Sarah',
            'last_name' => 'Johnson',
            'email' => 'sarah.johnson@cis.com',
            'phone_number' => '91234568',
            'address' => '456 Oak Avenue, Singapore 234567',
            'department_id' => $departments['Human Resources']->department_id,
            'designation_id' => $designations['Senior']->designation_id,
            'company' => 'CIS',
        ]);

        $cisEmployeeRecords[] = Employee::create([
            'user_id' => $cisEmployeeUsers[2]->user_id,
            'first_name' => 'Michael',
            'last_name' => 'Chen',
            'email' => 'michael.chen@cis.com',
            'phone_number' => '91234569',
            'address' => '789 Pine Road, Singapore 345678',
            'department_id' => $departments['Finance']->department_id,
            'designation_id' => $designations['Junior']->designation_id,
            'company' => 'CIS',
        ]);

        $cisEmployeeRecords[] = Employee::create([
            'user_id' => $cisEmployeeUsers[3]->user_id,
            'first_name' => 'Emily',
            'last_name' => 'Wong',
            'email' => 'emily.wong@cis.com',
            'phone_number' => '91234570',
            'address' => '321 Elm Street, Singapore 456789',
            'department_id' => $departments['Marketing']->department_id,
            'designation_id' => $designations['Senior']->designation_id,
            'company' => 'CIS',
        ]);

        $cisEmployeeRecords[] = Employee::create([
            'user_id' => $cisEmployeeUsers[4]->user_id,
            'first_name' => 'Alex',
            'last_name' => 'Tan',
            'email' => 'alex.tan@cis.com',
            'phone_number' => '91234571',
            'address' => '654 Maple Drive, Singapore 567890',
            'department_id' => $departments['Sales']->department_id,
            'designation_id' => $designations['Associate']->designation_id,
            'company' => 'CIS',
        ]);

        // Create CIS Vendors
        $cisVendors = [
            Vendor::create([
                'name' => 'CIS Tech Solutions Pte Ltd',
                'address' => '123 Tech Street, Singapore 123456',
                'contact_name' => 'John Tech',
                'contact_number' => '67890123',
                'company' => 'CIS',
            ]),
            Vendor::create([
                'name' => 'CIS Office Supplies Co',
                'address' => '456 Office Road, Singapore 234567',
                'contact_name' => 'Mary Office',
                'contact_number' => '67890124',
                'company' => 'CIS',
            ]),
            Vendor::create([
                'name' => 'CIS Equipment Services',
                'address' => '789 Equipment Lane, Singapore 345678',
                'contact_name' => 'Peter Equipment',
                'contact_number' => '67890125',
                'company' => 'CIS',
            ]),
        ];

        // Create CIS Suppliers
        $cisSuppliers = [
            Supplier::create([
                'name' => 'CIS Marketing Agency',
                'address' => '321 Marketing Ave, Singapore 456789',
                'contact_name' => 'Lisa Marketing',
                'contact_number' => '67890126',
                'company' => 'CIS',
            ]),
            Supplier::create([
                'name' => 'CIS Consulting Services',
                'address' => '654 Consulting Blvd, Singapore 567890',
                'contact_name' => 'David Consulting',
                'contact_number' => '67890127',
                'company' => 'CIS',
            ]),
            Supplier::create([
                'name' => 'CIS Software Solutions',
                'address' => '987 Software Way, Singapore 678901',
                'contact_name' => 'Anna Software',
                'contact_number' => '67890128',
                'company' => 'CIS',
            ]),
        ];

        // Get categories and payment modes
        $categories = Category::all()->keyBy('name');
        $paymentModes = ModeOfPayment::all()->keyBy('name');

        // Create CIS Claims
        $cisClaims = [
            // Claim 1: Employee Travel
            [
                'reference' => 'CIS-2508-001',
                'payee_type' => 'App\Models\Employee',
                'payee_id' => $cisEmployeeRecords[0]->employee_id,
                'total_amount' => 1250.00,
                'status' => 'approved',
                'items' => [
                    ['category' => 'Travel & Transportation', 'description' => 'Business trip to Kuala Lumpur - Flight tickets', 'amount' => 450.00],
                    ['category' => 'Travel & Transportation', 'description' => 'Business trip to Kuala Lumpur - Hotel accommodation', 'amount' => 800.00],
                ]
            ],
            // Claim 2: Office Supplies
            [
                'reference' => 'CIS-2508-002',
                'payee_type' => 'App\Models\Vendor',
                'payee_id' => $cisVendors[1]->vendor_id,
                'total_amount' => 850.50,
                'status' => 'approved',
                'items' => [
                    ['category' => 'Office Supplies', 'description' => 'Printer cartridges and paper', 'amount' => 350.50],
                    ['category' => 'Office Supplies', 'description' => 'Stationery and notebooks', 'amount' => 500.00],
                ]
            ],
            // Claim 3: Marketing Services
            [
                'reference' => 'CIS-2508-003',
                'payee_type' => 'App\Models\Supplier',
                'payee_id' => $cisSuppliers[0]->supplier_id,
                'total_amount' => 2500.00,
                'status' => 'approved',
                'items' => [
                    ['category' => 'Marketing & Advertising', 'description' => 'Digital marketing campaign - Social media advertising', 'amount' => 1500.00],
                    ['category' => 'Marketing & Advertising', 'description' => 'Digital marketing campaign - Google Ads', 'amount' => 1000.00],
                ]
            ],
            // Claim 4: Employee Training
            [
                'reference' => 'CIS-2508-004',
                'payee_type' => 'App\Models\Employee',
                'payee_id' => $cisEmployeeRecords[1]->employee_id,
                'total_amount' => 1800.00,
                'status' => 'submitted',
                'items' => [
                    ['category' => 'Training & Development', 'description' => 'Professional certification course - HR Management', 'amount' => 1200.00],
                    ['category' => 'Training & Development', 'description' => 'Training materials and books', 'amount' => 600.00],
                ]
            ],
            // Claim 5: IT Consulting
            [
                'reference' => 'CIS-2508-005',
                'payee_type' => 'App\Models\Supplier',
                'payee_id' => $cisSuppliers[1]->supplier_id,
                'total_amount' => 3200.00,
                'status' => 'draft',
                'items' => [
                    ['category' => 'Consulting Services', 'description' => 'IT infrastructure consulting services', 'amount' => 2000.00],
                    ['category' => 'Consulting Services', 'description' => 'System optimization and performance review', 'amount' => 1200.00],
                ]
            ],
            // Claim 6: Equipment Purchase
            [
                'reference' => 'CIS-2508-006',
                'payee_type' => 'App\Models\Vendor',
                'payee_id' => $cisVendors[2]->vendor_id,
                'total_amount' => 4500.00,
                'status' => 'approved',
                'items' => [
                    ['category' => 'Equipment & Maintenance', 'description' => 'New office chairs and desks', 'amount' => 3000.00],
                    ['category' => 'Equipment & Maintenance', 'description' => 'Air conditioning maintenance', 'amount' => 1500.00],
                ]
            ],
        ];

        $cisClaimRecords = [];
        foreach ($cisClaims as $claimData) {
            $claim = Claim::create([
                'reference_number' => $claimData['reference'],
                'payee_type' => $claimData['payee_type'],
                'payee_id' => $claimData['payee_id'],
                'total_amount' => $claimData['total_amount'],
                'status' => $claimData['status'],
                'submitted_at' => $claimData['status'] !== 'draft' ? now()->subDays(rand(1, 10)) : null,
                'reviewed_by' => $claimData['status'] === 'approved' ? $cisApprover->user_id : null,
                'reviewed_at' => $claimData['status'] === 'approved' ? now()->subDays(rand(1, 5)) : null,
                'company' => 'CIS',
            ]);

            foreach ($claimData['items'] as $item) {
                ClaimReference::create([
                    'claim_id' => $claim->claim_id,
                    'category_id' => $categories[$item['category']]->category_id,
                    'description' => $item['description'],
                    'expense_date' => now()->subDays(rand(5, 30)),
                    'amount' => $item['amount'],
                ]);
            }

            $cisClaimRecords[] = $claim;
        }

        // Create Vouchers for approved claims
        $approvedClaims = array_filter($cisClaimRecords, fn($claim) => $claim->status === 'approved');
        $voucherNumber = 1;
        foreach ($approvedClaims as $claim) {
            Voucher::create([
                'voucher_number' => 'CIS-V2508-' . str_pad($voucherNumber, 3, '0', STR_PAD_LEFT),
                'claim_id' => $claim->claim_id,
                'mode_of_payment_id' => $paymentModes['Bank Transfer']->mode_of_payment_id,
                'payment_date' => now()->subDays(rand(1, 5)),
                'remarks' => 'Payment for ' . strtolower($claim->reference_number),
                'approved_by' => $cisApprover->user_id,
                'created_by' => $cisAdmin->user_id,
                'company' => 'CIS',
            ]);
            $voucherNumber++;
        }

        $this->command->info("CIS: Created " . count($cisEmployeeRecords) . " employees, " . count($cisVendors) . " vendors, " . count($cisSuppliers) . " suppliers, " . count($cisClaimRecords) . " claims, " . count($approvedClaims) . " vouchers");
    }

    private function createSCSData()
    {
        $this->command->info('Creating SCS Company Data...');

        // Create SCS Users
        $scsAdmin = User::firstOrCreate([
            'email' => 'scs.admin@scs.com',
        ], [
            'name' => 'SCS Admin',
            'password' => Hash::make('password'),
        ]);

        $scsApprover = User::firstOrCreate([
            'email' => 'scs.approver@scs.com',
        ], [
            'name' => 'SCS Approver',
            'password' => Hash::make('password'),
        ]);

        // Create SCS Employee Users
        $scsEmployees = [
            ['email' => 'david.lee@scs.com', 'name' => 'David Lee', 'phone' => '91234572'],
            ['email' => 'jennifer.tan@scs.com', 'name' => 'Jennifer Tan', 'phone' => '91234573'],
            ['email' => 'robert.kumar@scs.com', 'name' => 'Robert Kumar', 'phone' => '91234574'],
            ['email' => 'lisa.ng@scs.com', 'name' => 'Lisa Ng', 'phone' => '91234575'],
            ['email' => 'kevin.wong@scs.com', 'name' => 'Kevin Wong', 'phone' => '91234576'],
        ];

        $scsEmployeeUsers = [];
        foreach ($scsEmployees as $employee) {
            $user = User::firstOrCreate([
                'email' => $employee['email'],
            ], [
                'name' => $employee['name'],
                'password' => Hash::make('password'),
            ]);
            $scsEmployeeUsers[] = $user;
        }

        // Get departments and designations
        $departments = Department::all()->keyBy('name');
        $designations = Designation::all()->keyBy('name');

        // Create SCS Employees
        $scsEmployeeRecords = [];
        $scsEmployeeRecords[] = Employee::create([
            'user_id' => $scsEmployeeUsers[0]->user_id,
            'first_name' => 'David',
            'last_name' => 'Lee',
            'email' => 'david.lee@scs.com',
            'phone_number' => '91234572',
            'address' => '111 SCS Street, Singapore 111111',
            'department_id' => $departments['Information Technology']->department_id,
            'designation_id' => $designations['Manager']->designation_id,
            'company' => 'SCS',
        ]);

        $scsEmployeeRecords[] = Employee::create([
            'user_id' => $scsEmployeeUsers[1]->user_id,
            'first_name' => 'Jennifer',
            'last_name' => 'Tan',
            'email' => 'jennifer.tan@scs.com',
            'phone_number' => '91234573',
            'address' => '222 SCS Avenue, Singapore 222222',
            'department_id' => $departments['Human Resources']->department_id,
            'designation_id' => $designations['Senior']->designation_id,
            'company' => 'SCS',
        ]);

        $scsEmployeeRecords[] = Employee::create([
            'user_id' => $scsEmployeeUsers[2]->user_id,
            'first_name' => 'Robert',
            'last_name' => 'Kumar',
            'email' => 'robert.kumar@scs.com',
            'phone_number' => '91234574',
            'address' => '333 SCS Road, Singapore 333333',
            'department_id' => $departments['Finance']->department_id,
            'designation_id' => $designations['Junior']->designation_id,
            'company' => 'SCS',
        ]);

        $scsEmployeeRecords[] = Employee::create([
            'user_id' => $scsEmployeeUsers[3]->user_id,
            'first_name' => 'Lisa',
            'last_name' => 'Ng',
            'email' => 'lisa.ng@scs.com',
            'phone_number' => '91234575',
            'address' => '444 SCS Boulevard, Singapore 444444',
            'department_id' => $departments['Marketing']->department_id,
            'designation_id' => $designations['Senior']->designation_id,
            'company' => 'SCS',
        ]);

        $scsEmployeeRecords[] = Employee::create([
            'user_id' => $scsEmployeeUsers[4]->user_id,
            'first_name' => 'Kevin',
            'last_name' => 'Wong',
            'email' => 'kevin.wong@scs.com',
            'phone_number' => '91234576',
            'address' => '555 SCS Drive, Singapore 555555',
            'department_id' => $departments['Sales']->department_id,
            'designation_id' => $designations['Associate']->designation_id,
            'company' => 'SCS',
        ]);

        // Create SCS Vendors
        $scsVendors = [
            Vendor::create([
                'name' => 'SCS Technology Solutions',
                'address' => '666 Tech Street, Singapore 666666',
                'contact_name' => 'Alex Tech',
                'contact_number' => '67890129',
                'company' => 'SCS',
            ]),
            Vendor::create([
                'name' => 'SCS Office Equipment',
                'address' => '777 Office Lane, Singapore 777777',
                'contact_name' => 'Betty Office',
                'contact_number' => '67890130',
                'company' => 'SCS',
            ]),
            Vendor::create([
                'name' => 'SCS Maintenance Services',
                'address' => '888 Maintenance Road, Singapore 888888',
                'contact_name' => 'Charlie Maintenance',
                'contact_number' => '67890131',
                'company' => 'SCS',
            ]),
        ];

        // Create SCS Suppliers
        $scsSuppliers = [
            Supplier::create([
                'name' => 'SCS Digital Marketing',
                'address' => '999 Digital Drive, Singapore 999999',
                'contact_name' => 'Diana Digital',
                'contact_number' => '67890132',
                'company' => 'SCS',
            ]),
            Supplier::create([
                'name' => 'SCS Business Consulting',
                'address' => '101 Business Way, Singapore 101010',
                'contact_name' => 'Edward Business',
                'contact_number' => '67890133',
                'company' => 'SCS',
            ]),
            Supplier::create([
                'name' => 'SCS Software Development',
                'address' => '202 Software Ave, Singapore 202020',
                'contact_name' => 'Fiona Software',
                'contact_number' => '67890134',
                'company' => 'SCS',
            ]),
        ];

        // Get categories and payment modes
        $categories = Category::all()->keyBy('name');
        $paymentModes = ModeOfPayment::all()->keyBy('name');

        // Create SCS Claims
        $scsClaims = [
            // Claim 1: Employee Travel
            [
                'reference' => 'SCS-2508-001',
                'payee_type' => 'App\Models\Employee',
                'payee_id' => $scsEmployeeRecords[0]->employee_id,
                'total_amount' => 1800.00,
                'status' => 'approved',
                'items' => [
                    ['category' => 'Travel & Transportation', 'description' => 'Business trip to Bangkok - Flight tickets', 'amount' => 650.00],
                    ['category' => 'Travel & Transportation', 'description' => 'Business trip to Bangkok - Hotel accommodation', 'amount' => 1150.00],
                ]
            ],
            // Claim 2: Office Equipment
            [
                'reference' => 'SCS-2508-002',
                'payee_type' => 'App\Models\Vendor',
                'payee_id' => $scsVendors[1]->vendor_id,
                'total_amount' => 1200.75,
                'status' => 'approved',
                'items' => [
                    ['category' => 'Office Supplies', 'description' => 'New monitors and keyboards', 'amount' => 800.75],
                    ['category' => 'Office Supplies', 'description' => 'Paper and ink cartridges', 'amount' => 400.00],
                ]
            ],
            // Claim 3: Website Development
            [
                'reference' => 'SCS-2508-003',
                'payee_type' => 'App\Models\Supplier',
                'payee_id' => $scsSuppliers[0]->supplier_id,
                'total_amount' => 3500.00,
                'status' => 'submitted',
                'items' => [
                    ['category' => 'IT & Software', 'description' => 'Website redesign and development', 'amount' => 2500.00],
                    ['category' => 'Marketing & Advertising', 'description' => 'SEO optimization services', 'amount' => 1000.00],
                ]
            ],
            // Claim 4: Employee Training
            [
                'reference' => 'SCS-2508-004',
                'payee_type' => 'App\Models\Employee',
                'payee_id' => $scsEmployeeRecords[1]->employee_id,
                'total_amount' => 2200.00,
                'status' => 'draft',
                'items' => [
                    ['category' => 'Training & Development', 'description' => 'Advanced project management certification', 'amount' => 1500.00],
                    ['category' => 'Training & Development', 'description' => 'Training materials and online courses', 'amount' => 700.00],
                ]
            ],
            // Claim 5: Software Licenses
            [
                'reference' => 'SCS-2508-005',
                'payee_type' => 'App\Models\Supplier',
                'payee_id' => $scsSuppliers[2]->supplier_id,
                'total_amount' => 2800.00,
                'status' => 'approved',
                'items' => [
                    ['category' => 'IT & Software', 'description' => 'Microsoft Office 365 licenses', 'amount' => 1800.00],
                    ['category' => 'IT & Software', 'description' => 'Adobe Creative Suite licenses', 'amount' => 1000.00],
                ]
            ],
            // Claim 6: Utilities
            [
                'reference' => 'SCS-2508-006',
                'payee_type' => 'App\Models\Vendor',
                'payee_id' => $scsVendors[2]->vendor_id,
                'total_amount' => 950.25,
                'status' => 'approved',
                'items' => [
                    ['category' => 'Utilities & Services', 'description' => 'Electricity and water bills', 'amount' => 650.25],
                    ['category' => 'Utilities & Services', 'description' => 'Internet and phone services', 'amount' => 300.00],
                ]
            ],
        ];

        $scsClaimRecords = [];
        foreach ($scsClaims as $claimData) {
            $claim = Claim::create([
                'reference_number' => $claimData['reference'],
                'payee_type' => $claimData['payee_type'],
                'payee_id' => $claimData['payee_id'],
                'total_amount' => $claimData['total_amount'],
                'status' => $claimData['status'],
                'submitted_at' => $claimData['status'] !== 'draft' ? now()->subDays(rand(1, 10)) : null,
                'reviewed_by' => $claimData['status'] === 'approved' ? $scsApprover->user_id : null,
                'reviewed_at' => $claimData['status'] === 'approved' ? now()->subDays(rand(1, 5)) : null,
                'company' => 'SCS',
            ]);

            foreach ($claimData['items'] as $item) {
                ClaimReference::create([
                    'claim_id' => $claim->claim_id,
                    'category_id' => $categories[$item['category']]->category_id,
                    'description' => $item['description'],
                    'expense_date' => now()->subDays(rand(5, 30)),
                    'amount' => $item['amount'],
                ]);
            }

            $scsClaimRecords[] = $claim;
        }

        // Create Vouchers for approved claims
        $approvedClaims = array_filter($scsClaimRecords, fn($claim) => $claim->status === 'approved');
        $voucherNumber = 1;
        foreach ($approvedClaims as $claim) {
            Voucher::create([
                'voucher_number' => 'SCS-V2508-' . str_pad($voucherNumber, 3, '0', STR_PAD_LEFT),
                'claim_id' => $claim->claim_id,
                'mode_of_payment_id' => $paymentModes['Bank Transfer']->mode_of_payment_id,
                'payment_date' => now()->subDays(rand(1, 5)),
                'remarks' => 'Payment for ' . strtolower($claim->reference_number),
                'approved_by' => $scsApprover->user_id,
                'created_by' => $scsAdmin->user_id,
                'company' => 'SCS',
            ]);
            $voucherNumber++;
        }

        $this->command->info("SCS: Created " . count($scsEmployeeRecords) . " employees, " . count($scsVendors) . " vendors, " . count($scsSuppliers) . " suppliers, " . count($scsClaimRecords) . " claims, " . count($approvedClaims) . " vouchers");
    }
}
