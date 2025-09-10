<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Transport',
                'description' => 'Transportation and travel related expenses'
            ],
            [
                'name' => 'Food',
                'description' => 'Food and dining expenses'
            ],
            [
                'name' => 'Lodging',
                'description' => 'Accommodation and hotel expenses'
            ],
            [
                'name' => 'Supplies',
                'description' => 'Office and general supplies'
            ],
            [
                'name' => 'Communication',
                'description' => 'Phone, internet, and communication expenses'
            ],
            [
                'name' => 'Entertainment',
                'description' => 'Entertainment and recreational expenses'
            ],
            [
                'name' => 'Miscellaneous',
                'description' => 'Other miscellaneous expenses'
            ],
            [
                'name' => 'Fuel',
                'description' => 'Fuel and gas expenses'
            ],
            [
                'name' => 'Medical',
                'description' => 'Medical and healthcare expenses'
            ],
            [
                'name' => 'Office Expenses',
                'description' => 'Office-related expenses and equipment'
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
} 