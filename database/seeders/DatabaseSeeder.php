<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\VendorSeeder;
use Database\Seeders\SupplierSeeder;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\EmployeeSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ModeOfPaymentSeeder;
use Database\Seeders\ClaimsAndVouchersSeeder;
use Database\Seeders\RoleAssignmentSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create test user only if it doesn't exist
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        // Seed departments, vendors, suppliers, and employees
        $this->call([
            SuperAdminSeeder::class, // Must be first to create permissions
            DepartmentSeeder::class,
            VendorSeeder::class,
            SupplierSeeder::class,
            EmployeeSeeder::class, // Creates users and assigns roles
            RoleAssignmentSeeder::class, // Assigns roles to existing users
            CategorySeeder::class,
            ModeOfPaymentSeeder::class,
            ClaimsAndVouchersSeeder::class,
        ]);
    }
}
