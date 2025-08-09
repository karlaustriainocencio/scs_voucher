<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Metro Hardware Supply',
                'address' => '111 Hardware Lane, Manila',
                'contact_name' => 'Fernando Cruz',
                'contact_number' => '+63 911 111 1111'
            ],
            [
                'name' => 'Industrial Materials Co.',
                'address' => '222 Industrial Road, Quezon City',
                'contact_name' => 'Lucia Fernandez',
                'contact_number' => '+63 922 222 2222'
            ],
            [
                'name' => 'Construction Supplies Ltd.',
                'address' => '333 Construction Blvd, Makati',
                'contact_name' => 'Antonio Mendoza',
                'contact_number' => '+63 933 333 3333'
            ],
            [
                'name' => 'Building Materials Express',
                'address' => '444 Building Street, Taguig',
                'contact_name' => 'Elena Santos',
                'contact_number' => '+63 944 444 4444'
            ],
            [
                'name' => 'Quality Tools & Equipment',
                'address' => '555 Quality Ave, Pasig',
                'contact_name' => 'Ramon Gonzales',
                'contact_number' => '+63 955 555 5555'
            ],
            [
                'name' => 'Professional Supplies Inc.',
                'address' => '666 Professional Drive, Mandaluyong',
                'contact_name' => 'Sofia Reyes',
                'contact_number' => '+63 966 666 6666'
            ],
            [
                'name' => 'Industrial Solutions Group',
                'address' => '777 Industrial Park, San Juan',
                'contact_name' => 'Jose Martinez',
                'contact_number' => '+63 977 777 7777'
            ],
            [
                'name' => 'Commercial Equipment Co.',
                'address' => '888 Commercial Plaza, Manila',
                'contact_name' => 'Teresa Lopez',
                'contact_number' => '+63 988 888 8888'
            ],
            [
                'name' => 'Manufacturing Supplies Ltd.',
                'address' => '999 Manufacturing Complex, Marikina',
                'contact_name' => 'Alberto Torres',
                'contact_number' => '+63 999 999 9999'
            ],
            [
                'name' => 'Industrial Equipment Express',
                'address' => '000 Equipment Street, Caloocan',
                'contact_name' => 'Rosa Garcia',
                'contact_number' => '+63 900 000 0000'
            ]
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
} 