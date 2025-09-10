<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Information Technology',
                'description' => 'Handles all IT-related operations and system maintenance'
            ],
            [
                'name' => 'Human Resources',
                'description' => 'Manages employee relations, recruitment, and HR policies'
            ],
            [
                'name' => 'Finance',
                'description' => 'Handles financial operations, budgeting, and accounting'
            ],
            [
                'name' => 'Marketing',
                'description' => 'Responsible for marketing strategies and brand management'
            ],
            [
                'name' => 'Operations',
                'description' => 'Manages day-to-day business operations and processes'
            ],
            [
                'name' => 'Sales',
                'description' => 'Handles customer acquisition and sales activities'
            ],
            [
                'name' => 'Customer Service',
                'description' => 'Provides customer support and maintains client relationships'
            ],
            [
                'name' => 'Research & Development',
                'description' => 'Focuses on innovation and product development'
            ],
            [
                'name' => 'Legal',
                'description' => 'Handles legal matters and compliance issues'
            ],
            [
                'name' => 'Administration',
                'description' => 'Manages administrative tasks and office operations'
            ]
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                ['name' => $department['name']],
                $department
            );
        }
    }
} 