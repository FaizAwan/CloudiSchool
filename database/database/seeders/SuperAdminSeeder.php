<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'superadmin',
            'email' => 'superadmin@school.com',
            'password' => Hash::make('superadmin'),
            'role' => 'superadmin',
            'email_verified_at' => now(),
        ]);
        
        echo "SuperAdmin user created successfully!\n";
        echo "Login ID: superadmin\n";
        echo "Password: superadmin\n";
        echo "Role: superadmin\n";
    }
}
