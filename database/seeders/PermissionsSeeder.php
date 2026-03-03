<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Issues
            ['name' => 'issues.view', 'description' => 'View issues'],
            ['name' => 'issues.create', 'description' => 'Create new issues'],
            ['name' => 'issues.update', 'description' => 'Update issues'],
            ['name' => 'issues.delete', 'description' => 'Delete issues'],
            ['name' => 'issues.close', 'description' => 'Close issues'],
            ['name' => 'issues.reopen', 'description' => 'Reopen closed issues'],
            ['name' => 'issues.export', 'description' => 'Export issues to PDF'],

            // Users
            ['name' => 'admin.users.view', 'description' => 'View users'],
            ['name' => 'admin.users.create', 'description' => 'Create users'],
            ['name' => 'admin.users.update', 'description' => 'Update users'],
            ['name' => 'admin.users.delete', 'description' => 'Delete users'],
            ['name' => 'admin.users.activate', 'description' => 'Activate/deactivate users'],

            // Roles
            ['name' => 'admin.roles.view', 'description' => 'View roles'],
            ['name' => 'admin.roles.update', 'description' => 'Assign roles to users'],

            // Permissions
            ['name' => 'admin.permissions.view', 'description' => 'View permissions'],
            ['name' => 'admin.permissions.update', 'description' => 'Assign permissions to roles'],

            // Departments
            ['name' => 'admin.departments.view', 'description' => 'View departments'],
            ['name' => 'admin.departments.create', 'description' => 'Create departments'],
            ['name' => 'admin.departments.update', 'description' => 'Update departments'],
            ['name' => 'admin.departments.delete', 'description' => 'Delete departments'],

            // Issue Types
            ['name' => 'admin.issue-types.view', 'description' => 'View issue types'],
            ['name' => 'admin.issue-types.create', 'description' => 'Create issue types'],
            ['name' => 'admin.issue-types.update', 'description' => 'Update issue types'],
            ['name' => 'admin.issue-types.delete', 'description' => 'Delete issue types'],

            // Reports
            ['name' => 'reports.view', 'description' => 'View reports'],
            ['name' => 'reports.monthly', 'description' => 'View monthly reports'],
            ['name' => 'reports.yearly', 'description' => 'View yearly reports'],
            ['name' => 'reports.logbook', 'description' => 'View logbook reports'],
            ['name' => 'reports.export', 'description' => 'Export reports'],

            // Graphs
            ['name' => 'graphs.view', 'description' => 'View graphs'],
            ['name' => 'statistics.view', 'description' => 'View statistics dashboard'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['description' => $permission['description']]
            );
        }

        // Assign all permissions to SuperAdmin
        $superAdmin = Role::where('name', 'SuperAdmin')->first();
        if ($superAdmin) {
            $allPermissionIds = Permission::pluck('id')->toArray();
            $superAdmin->permissions()->sync($allPermissionIds);
        }

        // Assign most permissions to Admin (except user deletion)
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $adminPermissions = Permission::where('name', '!=', 'admin.users.delete')->pluck('id')->toArray();
            $admin->permissions()->sync($adminPermissions);
        }

        // Assign basic permissions to Staff
        $staff = Role::where('name', 'Staff')->first();
        if ($staff) {
            $staffPermissions = Permission::whereIn('name', [
                'issues.view',
                'issues.create',
                'issues.update',
                'issues.close',
                'issues.reopen',
                'reports.view',
                'reports.monthly',
                'reports.yearly',
                'reports.logbook',
                'graphs.view',
                'statistics.view',
            ])->pluck('id')->toArray();
            $staff->permissions()->sync($staffPermissions);
        }
    }
}
