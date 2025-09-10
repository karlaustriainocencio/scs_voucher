<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            // Management Roles
            'Managing Director' => 'Managing Director - Highest level of management authority',
            'Operation Manager' => 'Operation Manager - Manages daily operations and processes',
            'Assistant Operation Manager' => 'Assistant Operation Manager - Supports operation management',
            'Accounts & HR Manager' => 'Accounts & HR Manager - Manages accounting and human resources',
            'Accreditation Admin Manager' => 'Accreditation Admin Manager - Manages accreditation processes',
            'Assistant Head of Certification' => 'Assistant Head of Certification - Supports certification management',
            
            // Sales & Marketing Roles
            'Sales & Marketing Manager' => 'Sales & Marketing Manager - Manages sales and marketing activities',
            'Sales & Marketing Executive' => 'Sales & Marketing Executive - Executes sales and marketing tasks',
            
            // Customer Service Roles
            'Senior Customer Executive' => 'Senior Customer Executive - Senior level customer service',
            'Senior Customer Service Executive' => 'Senior Customer Service Executive - Senior customer service management',
            'Customer Service Executive' => 'Customer Service Executive - Mid-level customer service',
            'Customer Service Officer' => 'Customer Service Officer - Entry-level customer service',
            
            // Administrative Roles
            'Administrator' => 'Administrator - System administration and management',
            'Admin Coordinator' => 'Admin Coordinator - Coordinates administrative tasks',
            'Training Officer' => 'Training Officer - Manages training programs and development',
            
            // Audit & Compliance
            'Auditor' => 'Auditor - Conducts audits and compliance checks',
            
            // System Roles (Super_admin is created separately by SuperAdminSeeder)
            // 'Super_admin' => 'Super Admin - Full system access and control',
        ];

        $this->command->info('Creating roles...');
        
        foreach ($roles as $roleName => $description) {
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                ['guard_name' => 'web']
            );
            
            if ($role->wasRecentlyCreated) {
                $this->command->info("✓ Created role: {$roleName}");
            } else {
                $this->command->line("• Role already exists: {$roleName}");
            }
        }
        
        $this->command->info('Role seeding completed successfully!');
        $this->command->info('Total roles in system: ' . Role::count());
    }
}
