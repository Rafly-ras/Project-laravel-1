<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            ['name' => 'Manage Products', 'slug' => 'manage-products'],
            ['name' => 'Manage Transactions', 'slug' => 'manage-transactions'],
            ['name' => 'View Reports', 'slug' => 'view-reports'],
            ['name' => 'Manage Employees', 'slug' => 'manage-employees'],
        ];

        $permissionModels = [];
        foreach ($permissions as $permission) {
            $permissionModels[$permission['slug']] = Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                ['name' => $permission['name']]
            );
        }

        // Create Roles
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $staff = Role::firstOrCreate(['name' => 'Staff']);
        $viewer = Role::firstOrCreate(['name' => 'Viewer']);

        // Sync Permissions to Roles (sync is idempotent and handles existing ones)
        $admin->permissions()->sync(array_column($permissionModels, 'id'));
        
        $staff->permissions()->sync([
            $permissionModels['manage-products']->id,
            $permissionModels['manage-transactions']->id,
            $permissionModels['view-reports']->id,
        ]);

        $viewer->permissions()->sync([
            $permissionModels['view-reports']->id,
        ]);

        // Assign Admin role to existing users or create a default one
        $defaultAdmin = User::where('email', 'admin@example.com')->first();
        if (!$defaultAdmin) {
            $defaultAdmin = User::create([
                'name' => 'System Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role_id' => $admin->id,
            ]);
        } else {
            $defaultAdmin->update(['role_id' => $admin->id]);
        }

        // Ensure users have a role (default to Viewer for safety)
        User::whereNull('role_id')->update(['role_id' => $viewer->id]);
    }
}
