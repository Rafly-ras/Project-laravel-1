<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the Admin role (assumes RolePermissionSeeder has run)
        $adminRole = Role::where('name', 'Admin')->first();

        if (!$adminRole) {
            // Fallback if role doesn't exist for some reason
            $adminRole = Role::create(['name' => 'Admin']);
        }

        $adminEmail = 'admin@example.com';
        
        $user = User::where('email', $adminEmail)->first();

        if (!$user) {
            User::create([
                'name' => 'Admin',
                'email' => $adminEmail,
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole->id
            ]);
        } else {
            // Update password if user already exists (to match user request)
            $user->update([
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole->id
            ]);
        }
    }
}
