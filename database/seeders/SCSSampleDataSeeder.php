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

class SCSSampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create SCS Users
        $adminUser = User::firstOrCreate([
            'email' => 'scs.admin@scs.com',
        ], [
            'name' => 'SCS Admin',
            'password' => Hash::make('password'),
        ]);

        $approverUser = User::firstOrCreate([
            'email' => 'scs.approver@scs.com',
        ], [
            'name' => 'SCS Approver',
            'password' => Hash::make('password'),
        ]);

        // Create SCS Employee Users
        $employeeUser1 = User::firstOrCreate([
            'email' => 'david.lee@scs.com',
        ], [
            'name' => 'David Lee',
            'password' => Hash::make('password'),
        ]);

        $employeeUser2 = User::firstOrCreate([
            'email' => 'jennifer.tan@scs.com',
        ], [
            'name' => 'Jennifer Tan',
            'password' => Hash::make('password'),
        ]);

        $employeeUser3 = User::firstOrCreate([
            'email' => 'robert.kumar@scs.com',
        ], [
            'name' => 'Robert Kumar',
            'password' => Hash::make('password'),
        ]);

        $employeeUser4 = User::firstOrCreate([
            'email' => 'lisa.ng@scs.com',
        ], [
            'name' => 'Lisa Ng',
            'password' => Hash::make('password'),
        ]);

        // Get existing departments and designations
        $itDept = Department::where('name', 'Information Technology')->first();
        $hrDept = Department::where('name', 'Human Resources')->first();
        $financeDept = Department::where('name', 'Finance')->first();
        $marketingDept = Department::where('name', 'Marketing')->first();

        $managerDesignation = Designation::where('name', 'Manager')->first();
        $seniorDesignation = Designation::where('name', 'Senior')->first();
        $juniorDesignation = Designation::where('name', 'Junior')->first();

        // Create SCS Employees
        $employee1 = Employee::create([
            'user_id' => $employeeUser1->user_id,
            'first_name' => 'David',
            'last_name' => 'Lee',
            'email' => 'david.lee@scs.com',
            'phone_number' => '91234571',
            'address' => '111 SCS Street, Singapore 111111',
            'department_id' => $itDept->department_id,
            'designation_id' => $managerDesignation->designation_id,
            'company' => 'SCS',
        ]);

        $employee2 = Employee::create([
            'user_id' => $employeeUser2->user_id,
            'first_name' => 'Jennifer',
            'last_name' => 'Tan',
            'email' => 'jennifer.tan@scs.com',
            'phone_number' => '91234572',
            'address' => '222 SCS Avenue, Singapore 222222',
            'department_id' => $hrDept->department_id,
            'designation_id' => $seniorDesignation->designation_id,
            'company' => 'SCS',
        ]);

        $employee3 = Employee::create([
            'user_id' => $employeeUser3->user_id,
            'first_name' => 'Robert',
            'last_name' => 'Kumar',
            'email' => 'robert.kumar@scs.com',
            'phone_number' => '91234573',
            'address' => '333 SCS Road, Singapore 333333',
            'department_id' => $financeDept->department_id,
            'designation_id' => $juniorDesignation->designation_id,
            'company' => 'SCS',
        ]);

        $employee4 = Employee::create([
            'user_id' => $employeeUser4->user_id,
            'first_name' => 'Lisa',
            'last_name' => 'Ng',
            'email' => 'lisa.ng@scs.com',
            'phone_number' => '91234574',
            'address' => '444 SCS Boulevard, Singapore 444444',
            'department_id' => $marketingDept->department_id,
            'designation_id' => $seniorDesignation->designation_id,
            'company' => 'SCS',
        ]);

        // Create SCS Vendors
        $vendor1 = Vendor::create([
            'name' => 'SCS Technology Solutions',
            'address' => '555 Tech Street, Singapore 555555',
            'contact_name' => 'Alex Tech',
            'contact_number' => '67890127',
            'company' => 'SCS',
        ]);

        $vendor2 = Vendor::create([
            'name' => 'SCS Office Equipment',
            'address' => '666 Office Lane, Singapore 666666',
            'contact_name' => 'Betty Office',
            'contact_number' => '67890128',
            'company' => 'SCS',
        ]);

        // Create SCS Suppliers
        $supplier1 = Supplier::create([
            'name' => 'SCS Digital Marketing',
            'address' => '777 Digital Drive, Singapore 777777',
            'contact_name' => 'Charlie Digital',
            'contact_number' => '67890129',
            'company' => 'SCS',
        ]);

        $supplier2 = Supplier::create([
            'name' => 'SCS Business Consulting',
            'address' => '888 Business Way, Singapore 888888',
            'contact_name' => 'Diana Business',
            'contact_number' => '67890130',
            'company' => 'SCS',
        ]);

        // Get existing categories and mode of payments
        $travelCategory = Category::where('name', 'Travel & Transportation')->first();
        $officeCategory = Category::where('name', 'Office Supplies')->first();
        $marketingCategory = Category::where('name', 'Marketing & Advertising')->first();
        $trainingCategory = Category::where('name', 'Training & Development')->first();
        $consultingCategory = Category::where('name', 'Consulting Services')->first();

        $bankTransfer = ModeOfPayment::where('name', 'Bank Transfer')->first();
        $cheque = ModeOfPayment::where('name', 'Cheque')->first();

        // Create SCS Claims with Claim References

        // Claim 1: Employee Travel Claim
        $claim1 = Claim::create([
            'reference_number' => 'SCS-2508-001',
            'payee_type' => 'App\Models\Employee',
            'payee_id' => $employee1->employee_id,
            'total_amount' => 1800.00,
            'status' => 'approved',
            'submitted_at' => now()->subDays(4),
            'reviewed_by' => $approverUser->user_id,
            'reviewed_at' => now()->subDays(2),
            'company' => 'SCS',
        ]);

        ClaimReference::create([
            'claim_id' => $claim1->claim_id,
            'category_id' => $travelCategory->category_id,
            'description' => 'Business trip to Bangkok - Flight tickets',
            'expense_date' => now()->subDays(8),
            'amount' => 650.00,
        ]);

        ClaimReference::create([
            'claim_id' => $claim1->claim_id,
            'category_id' => $travelCategory->category_id,
            'description' => 'Business trip to Bangkok - Hotel accommodation',
            'expense_date' => now()->subDays(7),
            'amount' => 1150.00,
        ]);

        // Claim 2: Vendor Office Supplies
        $claim2 = Claim::create([
            'reference_number' => 'SCS-2508-002',
            'payee_type' => 'App\Models\Vendor',
            'payee_id' => $vendor2->vendor_id,
            'total_amount' => 1200.75,
            'status' => 'approved',
            'submitted_at' => now()->subDays(7),
            'reviewed_by' => $approverUser->user_id,
            'reviewed_at' => now()->subDays(5),
            'company' => 'SCS',
        ]);

        ClaimReference::create([
            'claim_id' => $claim2->claim_id,
            'category_id' => $officeCategory->category_id,
            'description' => 'Office equipment - New monitors and keyboards',
            'expense_date' => now()->subDays(11),
            'amount' => 800.75,
        ]);

        ClaimReference::create([
            'claim_id' => $claim2->claim_id,
            'category_id' => $officeCategory->category_id,
            'description' => 'Office supplies - Paper and ink cartridges',
            'expense_date' => now()->subDays(10),
            'amount' => 400.00,
        ]);

        // Claim 3: Supplier Marketing Services
        $claim3 = Claim::create([
            'reference_number' => 'SCS-2508-003',
            'payee_type' => 'App\Models\Supplier',
            'payee_id' => $supplier1->supplier_id,
            'total_amount' => 3500.00,
            'status' => 'submitted',
            'submitted_at' => now()->subDays(3),
            'company' => 'SCS',
        ]);

        ClaimReference::create([
            'claim_id' => $claim3->claim_id,
            'category_id' => $marketingCategory->category_id,
            'description' => 'Website redesign and development',
            'expense_date' => now()->subDays(12),
            'amount' => 2500.00,
        ]);

        ClaimReference::create([
            'claim_id' => $claim3->claim_id,
            'category_id' => $marketingCategory->category_id,
            'description' => 'SEO optimization services',
            'expense_date' => now()->subDays(11),
            'amount' => 1000.00,
        ]);

        // Claim 4: Employee Training
        $claim4 = Claim::create([
            'reference_number' => 'SCS-2508-004',
            'payee_type' => 'App\Models\Employee',
            'payee_id' => $employee2->employee_id,
            'total_amount' => 2200.00,
            'status' => 'draft',
            'company' => 'SCS',
        ]);

        ClaimReference::create([
            'claim_id' => $claim4->claim_id,
            'category_id' => $trainingCategory->category_id,
            'description' => 'Advanced project management certification',
            'expense_date' => now()->subDays(18),
            'amount' => 1500.00,
        ]);

        ClaimReference::create([
            'claim_id' => $claim4->claim_id,
            'category_id' => $trainingCategory->category_id,
            'description' => 'Training materials and online courses',
            'expense_date' => now()->subDays(17),
            'amount' => 700.00,
        ]);

        // Create Vouchers for approved claims
        Voucher::create([
            'voucher_number' => 'SCS-V2508-001',
            'claim_id' => $claim1->claim_id,
            'mode_of_payment_id' => $bankTransfer->mode_of_payment_id,
            'payment_date' => now()->subDays(1),
            'remarks' => 'Payment for Bangkok business trip',
            'approved_by' => $approverUser->user_id,
            'created_by' => $adminUser->user_id,
            'company' => 'SCS',
        ]);

        Voucher::create([
            'voucher_number' => 'SCS-V2508-002',
            'claim_id' => $claim2->claim_id,
            'mode_of_payment_id' => $cheque->mode_of_payment_id,
            'payment_date' => now(),
            'remarks' => 'Payment for office equipment and supplies',
            'approved_by' => $approverUser->user_id,
            'created_by' => $adminUser->user_id,
            'company' => 'SCS',
        ]);

        $this->command->info('SCS Sample Data created successfully!');
        $this->command->info('Created:');
        $this->command->info('- 2 Users (Admin & Approver)');
        $this->command->info('- 4 Employees');
        $this->command->info('- 2 Vendors');
        $this->command->info('- 2 Suppliers');
        $this->command->info('- 4 Claims with 8 Claim References');
        $this->command->info('- 2 Vouchers');
    }
}
