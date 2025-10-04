<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a super admin if it doesn't exist
        Admin::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'username' => 'superadmin',
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
            ]
        );

        // Create a regular admin for testing
        Admin::firstOrCreate(
            ['username' => 'admin'],
            [
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        $this->command->info('Super Admin and Admin accounts created successfully!');
        $this->command->info('Super Admin - Username: superadmin, Password: password123');
        $this->command->info('Admin - Username: admin, Password: password123');
    }
}
