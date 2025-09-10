<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class ListRolesCommand extends Command
{
    protected $signature = 'roles:list';
    protected $description = 'List all available roles in the system';

    public function handle()
    {
        $this->info('Available Roles in the System:');
        $this->newLine();
        
        $roles = Role::orderBy('name')->get();
        
        if ($roles->isEmpty()) {
            $this->warn('No roles found in the system.');
            return 0;
        }
        
        $headers = ['ID', 'Role Name', 'Guard', 'Created At'];
        $rows = [];
        
        foreach ($roles as $role) {
            $rows[] = [
                $role->id,
                $role->name,
                $role->guard_name,
                $role->created_at->format('Y-m-d H:i:s'),
            ];
        }
        
        $this->table($headers, $rows);
        $this->newLine();
        $this->info("Total roles: {$roles->count()}");
        
        return 0;
    }
}
