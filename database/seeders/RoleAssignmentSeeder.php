<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Employee;

class RoleAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $roles = [
            'manager' => 'Manager - Can manage team and approve claims',
            'employee' => 'Employee - Can create and view own claims',
            'accountant' => 'Accountant - Can manage financial records',
            'hr_officer' => 'HR Officer - Can manage employee records',
            'admin' => 'Admin - Can manage system settings',
        ];

        foreach ($roles as $roleName => $description) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Assign roles to existing users based on their email patterns or other criteria
        $this->assignRolesToUsers();
    }

    private function assignRolesToUsers(): void
    {
        // Get all users with their employees
        $users = User::with('employee')->get();

        foreach ($users as $user) {
            // Skip super admin
            if ($user->email === 'admin@scs.com') {
                continue;
            }

            // Assign roles based on email patterns or other logic
            $role = $this->determineRoleForUser($user);
            
            if ($role) {
                $user->assignRole($role);
                $this->command->info("Assigned role '{$role}' to user: {$user->email}");
            }
        }
    }

    private function determineRoleForUser(User $user): ?string
    {
        // You can customize this logic based on your needs
        $email = strtolower($user->email);
        
        // Example role assignment logic
        if (str_contains($email, 'manager') || str_contains($email, 'ceo')) {
            return 'manager';
        }
        
        if (str_contains($email, 'hr') || str_contains($email, 'human')) {
            return 'hr_officer';
        }
        
        if (str_contains($email, 'accountant') || str_contains($email, 'finance')) {
            return 'accountant';
        }
        
        if (str_contains($email, 'admin')) {
            return 'admin';
        }
        
        // Default role for employees
        return 'employee';
    }
}
