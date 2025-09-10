<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-role {email} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a role to a user by email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $roleName = $this->argument('role');

        // Find the user
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return 1;
        }

        // Check if role exists
        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            $this->error("Role '{$roleName}' not found.");
            $this->info("Available roles: " . Role::pluck('name')->implode(', '));
            return 1;
        }

        // Assign role to user
        $user->assignRole($roleName);
        
        $this->info("Successfully assigned role '{$roleName}' to user '{$email}'");
        
        // Show user's current roles
        $userRoles = $user->roles->pluck('name')->implode(', ');
        $this->info("User's current roles: {$userRoles}");
        
        return 0;
    }
}
