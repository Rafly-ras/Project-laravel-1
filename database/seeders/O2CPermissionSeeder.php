<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class O2CPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Request Orders
            ['name' => 'View Request Orders', 'slug' => 'ro.view'],
            ['name' => 'Create Request Orders', 'slug' => 'ro.create'],
            ['name' => 'Edit Request Orders', 'slug' => 'ro.edit'],
            ['name' => 'Approve Request Orders', 'slug' => 'ro.approve'],
            ['name' => 'Convert RO to SO', 'slug' => 'ro.convert'],

            // Sales Orders
            ['name' => 'View Sales Orders', 'slug' => 'so.view'],
            ['name' => 'Create Sales Orders', 'slug' => 'so.create'],
            ['name' => 'Edit Sales Orders', 'slug' => 'so.edit'],
            ['name' => 'Confirm Sales Orders', 'slug' => 'so.confirm'],

            // Invoices
            ['name' => 'View Invoices', 'slug' => 'invoices.view'],
            ['name' => 'Create Invoices', 'slug' => 'invoices.create'],
            ['name' => 'Export Invoices', 'slug' => 'invoices.export'],

            // Payments
            ['name' => 'View Payments', 'slug' => 'payments.view'],
            ['name' => 'Record Payments', 'slug' => 'payments.create'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // Attach all to Admin and Manager role
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->syncWithoutDetaching(Permission::all());
        }

        $managerRole = Role::where('name', 'Manager')->first();
        if ($managerRole) {
            // Managers can do most O2C but maybe not delete everything? 
            // For now let's give them all O2C as per requirement "Only Admin/Manager can approve"
            $managerRole->permissions()->syncWithoutDetaching(Permission::all());
        }
    }
}
