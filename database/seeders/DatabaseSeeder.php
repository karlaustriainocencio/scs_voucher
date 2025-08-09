<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\VendorSeeder;
use Database\Seeders\SupplierSeeder;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\DesignationSeeder;
use Database\Seeders\EmployeeSeeder;
use Database\Seeders\CategorySeeder;
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

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed departments, designations, vendors, suppliers, and employees
        $this->call([
            DepartmentSeeder::class,
            DesignationSeeder::class,
            VendorSeeder::class,
            SupplierSeeder::class,
            EmployeeSeeder::class,
            CategorySeeder::class,
        ]);
    }
}
