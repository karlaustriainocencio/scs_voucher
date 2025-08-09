<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            [
                'name' => 'ABC Office Supplies',
                'address' => '123 Main Street, Metro Manila',
                'contact_name' => 'Juan Dela Cruz',
                'contact_number' => '+63 912 345 6789'
            ],
            [
                'name' => 'Tech Solutions Inc.',
                'address' => '456 Business Ave, Quezon City',
                'contact_name' => 'Maria Santos',
                'contact_number' => '+63 923 456 7890'
            ],
            [
                'name' => 'Global Electronics',
                'address' => '789 Tech Park, Makati City',
                'contact_name' => 'Pedro Martinez',
                'contact_number' => '+63 934 567 8901'
            ],
            [
                'name' => 'Office Plus Corporation',
                'address' => '321 Corporate Plaza, Taguig',
                'contact_name' => 'Ana Reyes',
                'contact_number' => '+63 945 678 9012'
            ],
            [
                'name' => 'Digital Innovations Co.',
                'address' => '654 Innovation Drive, Pasig City',
                'contact_name' => 'Carlos Lopez',
                'contact_number' => '+63 956 789 0123'
            ],
            [
                'name' => 'Premium Office Solutions',
                'address' => '987 Executive Blvd, Mandaluyong',
                'contact_name' => 'Isabella Garcia',
                'contact_number' => '+63 967 890 1234'
            ],
            [
                'name' => 'Smart Tech Enterprises',
                'address' => '147 Smart City, San Juan',
                'contact_name' => 'Miguel Torres',
                'contact_number' => '+63 978 901 2345'
            ],
            [
                'name' => 'Elite Business Services',
                'address' => '258 Elite Tower, Manila',
                'contact_name' => 'Carmen Rodriguez',
                'contact_number' => '+63 989 012 3456'
            ],
            [
                'name' => 'Future Systems Ltd.',
                'address' => '369 Future Complex, Marikina',
                'contact_name' => 'Roberto Silva',
                'contact_number' => '+63 990 123 4567'
            ],
            [
                'name' => 'Advanced Solutions Group',
                'address' => '741 Advanced Park, Caloocan',
                'contact_name' => 'Patricia Morales',
                'contact_number' => '+63 901 234 5678'
            ]
        ];

        foreach ($vendors as $vendor) {
            Vendor::create($vendor);
        }
    }
} 