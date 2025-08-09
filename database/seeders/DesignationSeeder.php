<?php

namespace Database\Seeders;

use App\Models\Designation;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $designations = [
            [
                'name' => 'Chief Executive Officer',
                'description' => 'Top executive responsible for overall company strategy'
            ],
            [
                'name' => 'Manager',
                'description' => 'Supervises team and manages department operations'
            ],
            [
                'name' => 'Senior Developer',
                'description' => 'Experienced software developer with advanced skills'
            ],
            [
                'name' => 'Accountant',
                'description' => 'Handles financial records and accounting tasks'
            ],
            [
                'name' => 'Marketing Specialist',
                'description' => 'Develops and implements marketing strategies'
            ],
            [
                'name' => 'Sales Representative',
                'description' => 'Generates sales and maintains customer relationships'
            ],
            [
                'name' => 'Customer Service Representative',
                'description' => 'Provides customer support and resolves issues'
            ],
            [
                'name' => 'Human Resources Officer',
                'description' => 'Manages HR functions and employee relations'
            ],
            [
                'name' => 'Administrative Assistant',
                'description' => 'Provides administrative support and office management'
            ],
            [
                'name' => 'Junior Developer',
                'description' => 'Entry-level software developer'
            ]
        ];

        foreach ($designations as $designation) {
            Designation::create($designation);
        }
    }
} 