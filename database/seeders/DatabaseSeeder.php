<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\VendorSeeder;
use Database\Seeders\SupplierSeeder;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\DesignationSeeder;
use Database\Seeders\EmployeeSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ModeOfPaymentSeeder;
use Database\Seeders\ClaimsAndVouchersSeeder;
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

        // Seed departments, designations, vendors, suppliers, and employees
        $this->call([
            DepartmentSeeder::class,
            DesignationSeeder::class,
            VendorSeeder::class,
            SupplierSeeder::class,
            EmployeeSeeder::class,
            CategorySeeder::class,
            ModeOfPaymentSeeder::class,
            ClaimsAndVouchersSeeder::class,
        ]);
    }
}
