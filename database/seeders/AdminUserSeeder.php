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
        $adminEmail = env('DEFAULT_ADMIN_EMAIL', 'admin@example.com');
        $adminPassword = env('DEFAULT_ADMIN_PASSWORD', 'admin123');

        // Get Admin role
        $adminRole = Role::where('name', 'Admin')->first();

        // Check if admin user already exists
        $user = User::where('email', $adminEmail)->first();

        if (!$user && $adminRole) {
            User::create([
                'name' => 'Admin',
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'role_id' => $adminRole->id,
            ]);
        } elseif ($user && $adminRole) {
            // Update role and password if it exists (ensures seeder works as intended)
            $user->update([
                'password' => Hash::make($adminPassword),
                'role_id' => $adminRole->id,
            ]);
        }
    }
}
