<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class BrowserTestSeeder extends Seeder
{
    public function run()
    {
        // 1. Superadmin (superadmin / superadmin)
        $sa = User::where('name', 'superadmin')->orWhere('email', 'superadmin@superschool.com')->first();
        if (!$sa) {
            User::create([
                'name' => 'superadmin', // Used for login_id
                'email' => 'superadmin@superschool.com',
                'password' => Hash::make('superadmin'),
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ]);
            $this->command->info('Superadmin created: superadmin / superadmin');
        } else {
            $sa->update([
                'name' => 'superadmin', 
                'password' => Hash::make('superadmin')
            ]);
            $this->command->info('Superadmin updated: superadmin / superadmin');
        }

        // 2. Tenant School & Admin (admin@school.com / password)
        // Ensure Tenant model exists (check for error prevention)
        if (class_exists(\App\Models\Tenant::class)) {
             $tenantId = 9999;
             if (!\App\Models\Tenant::find($tenantId)) {
                 \App\Models\Tenant::create(['id' => $tenantId, 'data' => ['name' => 'Browser Test School']]);
             }
        } else {
             $tenantId = 9999; // Fallback if no Tenant model (unlikely given previous contexts)
        }

        $school = School::find($tenantId); // Assuming id matches
        if (!$school) {
             $school = School::create([
                 'id' => $tenantId, // Force ID to match tenant
                 'schoolName' => 'Browser Test School',
                 'tenant_id' => $tenantId,
                 'schoolCity' => 'Test City',
                 'address' => '123 Test St',
                 'schoolAdminName' => 'Test Admin',
                 'schoolAdminEmail' => 'admin@school.com',
                 'schoolAdminPassword' => bcrypt('password'),
                 'status' => 'active',
             ]);
        }

        $ta = User::where('email', 'admin@school.com')->first();
        if (!$ta) {
            User::create([
                'name' => 'Test Admin',
                'email' => 'admin@school.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'tenant_id' => $tenantId,
                'school_id' => $tenantId,
                'email_verified_at' => now(),
            ]);
            $this->command->info('Tenant Admin created: admin@school.com / password');
        } else {
            $ta->update([
                'password' => Hash::make('password'),
                'tenant_id' => $tenantId,
                'school_id' => $tenantId
            ]);
            $this->command->info('Tenant Admin updated: admin@school.com / password');
        }
    }
}
