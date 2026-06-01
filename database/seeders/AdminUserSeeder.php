<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@school.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        
        echo "Admin user created successfully!\n";
        echo "Email: admin@school.com\n";
        echo "Password: admin123\n";
    }
}
