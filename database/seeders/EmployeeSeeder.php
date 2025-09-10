<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing employees and their users to avoid conflicts
        Employee::query()->delete();
        User::where('email', 'like', '%@company.com')->delete();
        
        // Get department IDs
        $departments = Department::pluck('department_id')->toArray();
        
        // Roles are now created by RoleSeeder

        $employees = [
            [
                'first_name' => 'Karl',
                'last_name' => 'Inocencio',
                'email' => 'karl@socotec.com',
                'phone_number' => '9000000',
                'address' => 'Socotec Office, Philippines',
                'department_id' => $departments[0], // IT
                'role' => 'super_admin', // Super Admin
            ],
            [
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'email' => 'juan.delacruz@company.com',
                'phone_number' => '9123456',
                'address' => '123 Main Street, Quezon City',
                'department_id' => $departments[0], // IT
                'role' => 'Administrator', // System Administrator
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'email' => 'maria.santos@company.com',
                'phone_number' => '9234567',
                'address' => '456 Business Ave, Makati City',
                'department_id' => $departments[1], // HR
                'role' => 'Accounts & HR Manager', // HR Manager
            ],
            [
                'first_name' => 'Pedro',
                'last_name' => 'Martinez',
                'email' => 'pedro.martinez@company.com',
                'phone_number' => '9345678',
                'address' => '789 Finance Street, Taguig',
                'department_id' => $departments[2], // Finance
                'role' => 'Auditor', // Auditor
            ],
            [
                'first_name' => 'Ana',
                'last_name' => 'Reyes',
                'email' => 'ana.reyes@company.com',
                'phone_number' => '9456789',
                'address' => '321 Marketing Blvd, Pasig',
                'department_id' => $departments[3], // Marketing
                'role' => 'Sales & Marketing Executive', // Marketing Specialist
            ],
            [
                'first_name' => 'Carlos',
                'last_name' => 'Lopez',
                'email' => 'carlos.lopez@company.com',
                'phone_number' => '9567890',
                'address' => '654 Sales Drive, Mandaluyong',
                'department_id' => $departments[5], // Sales
                'role' => 'Sales & Marketing Executive', // Sales Representative
            ],
            [
                'first_name' => 'Isabella',
                'last_name' => 'Garcia',
                'email' => 'isabella.garcia@company.com',
                'phone_number' => '9678901',
                'address' => '987 Customer Service Ave, San Juan',
                'department_id' => $departments[6], // Customer Service
                'role' => 'Customer Service Executive', // Customer Service Representative
            ],
            [
                'first_name' => 'Miguel',
                'last_name' => 'Torres',
                'email' => 'miguel.torres@company.com',
                'phone_number' => '9789012',
                'address' => '147 Operations Street, Manila',
                'department_id' => $departments[4], // Operations
                'role' => 'Operation Manager', // Manager
            ],
            [
                'first_name' => 'Carmen',
                'last_name' => 'Rodriguez',
                'email' => 'carmen.rodriguez@company.com',
                'phone_number' => '9890123',
                'address' => '258 Admin Plaza, Marikina',
                'department_id' => $departments[9], // Administration
                'role' => 'Admin Coordinator', // Administrative Assistant
            ],
            [
                'first_name' => 'Roberto',
                'last_name' => 'Silva',
                'email' => 'roberto.silva@company.com',
                'phone_number' => '9901234',
                'address' => '369 R&D Complex, Caloocan',
                'department_id' => $departments[7], // R&D
                'role' => 'Administrator', // Senior Developer
            ],
            [
                'first_name' => 'Patricia',
                'last_name' => 'Morales',
                'email' => 'patricia.morales@company.com',
                'phone_number' => '9012345',
                'address' => '741 Legal Tower, Quezon City',
                'department_id' => $departments[8], // Legal
                'role' => 'Managing Director', // CEO
            ]
        ];

        foreach ($employees as $employeeData) {
            // Check if user already exists
            $existingUser = User::where('email', $employeeData['email'])->first();
            
            if ($existingUser) {
                // Use existing user
                $user = $existingUser;
            } else {
                // Create new user
                $user = User::create([
                    'name' => $employeeData['first_name'] . ' ' . $employeeData['last_name'],
                    'email' => $employeeData['email'],
                    'password' => Hash::make('password123'), // Default password
                ]);
            }

            // Assign role to user
            if (isset($employeeData['role'])) {
                $user->assignRole($employeeData['role']);
                $this->command->info("Assigned role '{$employeeData['role']}' to user: {$user->email}");
            }

            // Check if employee already exists
            $existingEmployee = Employee::where('user_id', $user->user_id)->first();
            
            if (!$existingEmployee) {
                // Create employee with user relationship
                Employee::create([
                    'user_id' => $user->user_id,
                    'first_name' => $employeeData['first_name'],
                    'last_name' => $employeeData['last_name'],
                    'email' => $employeeData['email'],
                    'phone_number' => $employeeData['phone_number'],
                    'address' => $employeeData['address'],
                    'department_id' => $employeeData['department_id'],
                ]);
            }
        }
    }

} 