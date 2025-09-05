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

class CISSampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create CIS Users
        $adminUser = User::firstOrCreate([
            'email' => 'cis.admin@cis.com',
        ], [
            'name' => 'CIS Admin',
            'password' => Hash::make('password'),
        ]);

        $approverUser = User::firstOrCreate([
            'email' => 'cis.approver@cis.com',
        ], [
            'name' => 'CIS Approver',
            'password' => Hash::make('password'),
        ]);

        // Create CIS Departments
        $itDept = Department::firstOrCreate([
            'name' => 'Information Technology',
        ], [
            'description' => 'IT Department for CIS',
        ]);

        $hrDept = Department::firstOrCreate([
            'name' => 'Human Resources',
        ], [
            'description' => 'HR Department for CIS',
        ]);

        $financeDept = Department::firstOrCreate([
            'name' => 'Finance',
        ], [
            'description' => 'Finance Department for CIS',
        ]);

        $marketingDept = Department::firstOrCreate([
            'name' => 'Marketing',
        ], [
            'description' => 'Marketing Department for CIS',
        ]);

        // Create CIS Designations
        $managerDesignation = Designation::firstOrCreate([
            'name' => 'Manager',
        ], [
            'description' => 'Manager position',
        ]);

        $seniorDesignation = Designation::firstOrCreate([
            'name' => 'Senior',
        ], [
            'description' => 'Senior position',
        ]);

        $juniorDesignation = Designation::firstOrCreate([
            'name' => 'Junior',
        ], [
            'description' => 'Junior position',
        ]);

        // Create CIS Employee Users
        $employeeUser1 = User::firstOrCreate([
            'email' => 'john.smith@cis.com',
        ], [
            'name' => 'John Smith',
            'password' => Hash::make('password'),
        ]);

        $employeeUser2 = User::firstOrCreate([
            'email' => 'sarah.johnson@cis.com',
        ], [
            'name' => 'Sarah Johnson',
            'password' => Hash::make('password'),
        ]);

        $employeeUser3 = User::firstOrCreate([
            'email' => 'michael.chen@cis.com',
        ], [
            'name' => 'Michael Chen',
            'password' => Hash::make('password'),
        ]);

        $employeeUser4 = User::firstOrCreate([
            'email' => 'emily.wong@cis.com',
        ], [
            'name' => 'Emily Wong',
            'password' => Hash::make('password'),
        ]);

        // Create CIS Employees
        $employee1 = Employee::create([
            'user_id' => $employeeUser1->user_id,
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john.smith@cis.com',
            'phone_number' => '91234567',
            'address' => '123 Main Street, Singapore 123456',
            'department_id' => $itDept->department_id,
            'designation_id' => $managerDesignation->designation_id,
            'company' => 'CIS',
        ]);

        $employee2 = Employee::create([
            'user_id' => $employeeUser2->user_id,
            'first_name' => 'Sarah',
            'last_name' => 'Johnson',
            'email' => 'sarah.johnson@cis.com',
            'phone_number' => '91234568',
            'address' => '456 Oak Avenue, Singapore 234567',
            'department_id' => $hrDept->department_id,
            'designation_id' => $seniorDesignation->designation_id,
            'company' => 'CIS',
        ]);

        $employee3 = Employee::create([
            'user_id' => $employeeUser3->user_id,
            'first_name' => 'Michael',
            'last_name' => 'Chen',
            'email' => 'michael.chen@cis.com',
            'phone_number' => '91234569',
            'address' => '789 Pine Road, Singapore 345678',
            'department_id' => $financeDept->department_id,
            'designation_id' => $juniorDesignation->designation_id,
            'company' => 'CIS',
        ]);

        $employee4 = Employee::create([
            'user_id' => $employeeUser4->user_id,
            'first_name' => 'Emily',
            'last_name' => 'Wong',
            'email' => 'emily.wong@cis.com',
            'phone_number' => '91234570',
            'address' => '321 Elm Street, Singapore 456789',
            'department_id' => $marketingDept->department_id,
            'designation_id' => $seniorDesignation->designation_id,
            'company' => 'CIS',
        ]);

        // Create CIS Vendors
        $vendor1 = Vendor::create([
            'name' => 'CIS Tech Solutions Pte Ltd',
            'address' => '123 Tech Street, Singapore 123456',
            'contact_name' => 'John Tech',
            'contact_number' => '67890123',
            'company' => 'CIS',
        ]);

        $vendor2 = Vendor::create([
            'name' => 'CIS Office Supplies Co',
            'address' => '456 Office Road, Singapore 234567',
            'contact_name' => 'Mary Office',
            'contact_number' => '67890124',
            'company' => 'CIS',
        ]);

        // Create CIS Suppliers
        $supplier1 = Supplier::create([
            'name' => 'CIS Marketing Agency',
            'address' => '789 Marketing Ave, Singapore 345678',
            'contact_name' => 'Peter Marketing',
            'contact_number' => '67890125',
            'company' => 'CIS',
        ]);

        $supplier2 = Supplier::create([
            'name' => 'CIS Consulting Services',
            'address' => '321 Consulting Blvd, Singapore 456789',
            'contact_name' => 'Lisa Consulting',
            'contact_number' => '67890126',
            'company' => 'CIS',
        ]);

        // Create Categories (shared across companies)
        $travelCategory = Category::firstOrCreate([
            'name' => 'Travel & Transportation',
        ], [
            'description' => 'Travel expenses and transportation costs',
        ]);

        $officeCategory = Category::firstOrCreate([
            'name' => 'Office Supplies',
        ], [
            'description' => 'Office supplies and equipment',
        ]);

        $marketingCategory = Category::firstOrCreate([
            'name' => 'Marketing & Advertising',
        ], [
            'description' => 'Marketing and advertising expenses',
        ]);

        $trainingCategory = Category::firstOrCreate([
            'name' => 'Training & Development',
        ], [
            'description' => 'Training and development costs',
        ]);

        $consultingCategory = Category::firstOrCreate([
            'name' => 'Consulting Services',
        ], [
            'description' => 'Professional consulting services',
        ]);

        // Create Mode of Payments
        $bankTransfer = ModeOfPayment::firstOrCreate([
            'name' => 'Bank Transfer',
        ], [
            'description' => 'Direct bank transfer',
        ]);

        $cheque = ModeOfPayment::firstOrCreate([
            'name' => 'Cheque',
        ], [
            'description' => 'Payment by cheque',
        ]);

        // Create CIS Claims with Claim References

        // Claim 1: Employee Travel Claim
        $claim1 = Claim::create([
            'reference_number' => 'CIS-2508-001',
            'payee_type' => 'App\Models\Employee',
            'payee_id' => $employee1->employee_id,
            'total_amount' => 1250.00,
            'status' => 'approved',
            'submitted_at' => now()->subDays(5),
            'reviewed_by' => $approverUser->user_id,
            'reviewed_at' => now()->subDays(3),
            'company' => 'CIS',
        ]);

        ClaimReference::create([
            'claim_id' => $claim1->claim_id,
            'category_id' => $travelCategory->category_id,
            'description' => 'Business trip to Kuala Lumpur - Flight tickets',
            'expense_date' => now()->subDays(10),
            'amount' => 450.00,
        ]);

        ClaimReference::create([
            'claim_id' => $claim1->claim_id,
            'category_id' => $travelCategory->category_id,
            'description' => 'Business trip to Kuala Lumpur - Hotel accommodation',
            'expense_date' => now()->subDays(9),
            'amount' => 800.00,
        ]);

        // Claim 2: Vendor Office Supplies
        $claim2 = Claim::create([
            'reference_number' => 'CIS-2508-002',
            'payee_type' => 'App\Models\Vendor',
            'payee_id' => $vendor2->vendor_id,
            'total_amount' => 850.50,
            'status' => 'approved',
            'submitted_at' => now()->subDays(8),
            'reviewed_by' => $approverUser->user_id,
            'reviewed_at' => now()->subDays(6),
            'company' => 'CIS',
        ]);

        ClaimReference::create([
            'claim_id' => $claim2->claim_id,
            'category_id' => $officeCategory->category_id,
            'description' => 'Office supplies - Printer cartridges and paper',
            'expense_date' => now()->subDays(12),
            'amount' => 350.50,
        ]);

        ClaimReference::create([
            'claim_id' => $claim2->claim_id,
            'category_id' => $officeCategory->category_id,
            'description' => 'Office supplies - Stationery and notebooks',
            'expense_date' => now()->subDays(11),
            'amount' => 500.00,
        ]);

        // Claim 3: Supplier Marketing Services
        $claim3 = Claim::create([
            'reference_number' => 'CIS-2508-003',
            'payee_type' => 'App\Models\Supplier',
            'payee_id' => $supplier1->supplier_id,
            'total_amount' => 2500.00,
            'status' => 'approved',
            'submitted_at' => now()->subDays(6),
            'reviewed_by' => $approverUser->user_id,
            'reviewed_at' => now()->subDays(4),
            'company' => 'CIS',
        ]);

        ClaimReference::create([
            'claim_id' => $claim3->claim_id,
            'category_id' => $marketingCategory->category_id,
            'description' => 'Digital marketing campaign - Social media advertising',
            'expense_date' => now()->subDays(15),
            'amount' => 1500.00,
        ]);

        ClaimReference::create([
            'claim_id' => $claim3->claim_id,
            'category_id' => $marketingCategory->category_id,
            'description' => 'Digital marketing campaign - Google Ads',
            'expense_date' => now()->subDays(14),
            'amount' => 1000.00,
        ]);

        // Claim 4: Employee Training
        $claim4 = Claim::create([
            'reference_number' => 'CIS-2508-004',
            'payee_type' => 'App\Models\Employee',
            'payee_id' => $employee2->employee_id,
            'total_amount' => 1800.00,
            'status' => 'submitted',
            'submitted_at' => now()->subDays(2),
            'company' => 'CIS',
        ]);

        ClaimReference::create([
            'claim_id' => $claim4->claim_id,
            'category_id' => $trainingCategory->category_id,
            'description' => 'Professional certification course - HR Management',
            'expense_date' => now()->subDays(20),
            'amount' => 1200.00,
        ]);

        ClaimReference::create([
            'claim_id' => $claim4->claim_id,
            'category_id' => $trainingCategory->category_id,
            'description' => 'Training materials and books',
            'expense_date' => now()->subDays(19),
            'amount' => 600.00,
        ]);

        // Claim 5: Supplier Consulting Services
        $claim5 = Claim::create([
            'reference_number' => 'CIS-2508-005',
            'payee_type' => 'App\Models\Supplier',
            'payee_id' => $supplier2->supplier_id,
            'total_amount' => 3200.00,
            'status' => 'draft',
            'company' => 'CIS',
        ]);

        ClaimReference::create([
            'claim_id' => $claim5->claim_id,
            'category_id' => $consultingCategory->category_id,
            'description' => 'IT infrastructure consulting services',
            'expense_date' => now()->subDays(25),
            'amount' => 2000.00,
        ]);

        ClaimReference::create([
            'claim_id' => $claim5->claim_id,
            'category_id' => $consultingCategory->category_id,
            'description' => 'System optimization and performance review',
            'expense_date' => now()->subDays(24),
            'amount' => 1200.00,
        ]);

        // Create Vouchers for approved claims
        Voucher::create([
            'voucher_number' => 'CIS-V2508-001',
            'claim_id' => $claim1->claim_id,
            'mode_of_payment_id' => $bankTransfer->mode_of_payment_id,
            'payment_date' => now()->subDays(2),
            'remarks' => 'Payment for business travel expenses',
            'approved_by' => $approverUser->user_id,
            'created_by' => $adminUser->user_id,
            'company' => 'CIS',
        ]);

        Voucher::create([
            'voucher_number' => 'CIS-V2508-002',
            'claim_id' => $claim2->claim_id,
            'mode_of_payment_id' => $cheque->mode_of_payment_id,
            'payment_date' => now()->subDays(1),
            'remarks' => 'Payment for office supplies',
            'approved_by' => $approverUser->user_id,
            'created_by' => $adminUser->user_id,
            'company' => 'CIS',
        ]);

        Voucher::create([
            'voucher_number' => 'CIS-V2508-003',
            'claim_id' => $claim3->claim_id,
            'mode_of_payment_id' => $bankTransfer->mode_of_payment_id,
            'payment_date' => now(),
            'remarks' => 'Payment for marketing services',
            'approved_by' => $approverUser->user_id,
            'created_by' => $adminUser->user_id,
            'company' => 'CIS',
        ]);

        $this->command->info('CIS Sample Data created successfully!');
        $this->command->info('Created:');
        $this->command->info('- 2 Users (Admin & Approver)');
        $this->command->info('- 4 Departments');
        $this->command->info('- 3 Designations');
        $this->command->info('- 4 Employees');
        $this->command->info('- 2 Vendors');
        $this->command->info('- 2 Suppliers');
        $this->command->info('- 5 Categories');
        $this->command->info('- 2 Mode of Payments');
        $this->command->info('- 5 Claims with 10 Claim References');
        $this->command->info('- 3 Vouchers');
    }
}
