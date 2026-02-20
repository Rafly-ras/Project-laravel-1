<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Products
            ['name' => 'View Products', 'slug' => 'products.view'],
            ['name' => 'Create Products', 'slug' => 'products.create'],
            ['name' => 'Edit Products', 'slug' => 'products.edit'],
            ['name' => 'Delete Products', 'slug' => 'products.delete'],
            ['name' => 'View Stock Summary', 'slug' => 'products.stock-summary'],

            // Categories
            ['name' => 'Manage Categories', 'slug' => 'categories.manage'],

            // Transactions
            ['name' => 'View Transactions', 'slug' => 'transactions.view'],
            ['name' => 'Record Transactions', 'slug' => 'transactions.create'],
            ['name' => 'Export Transactions', 'slug' => 'transactions.export'],

            // Warehouses
            ['name' => 'View Warehouses', 'slug' => 'warehouses.view'],
            ['name' => 'Manage Warehouses', 'slug' => 'warehouses.manage'],

            // Employees & RBAC
            ['name' => 'Manage Employees', 'slug' => 'employees.manage'],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage'],

            // Reports & Dashboard
            ['name' => 'View Reports', 'slug' => 'reports.view'],
            ['name' => 'View Cashflow', 'slug' => 'reports.cashflow'],
            ['name' => 'View Activity Logs', 'slug' => 'logs.view'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // Attach all to Admin role
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->sync(Permission::all());
        }
    }
}
