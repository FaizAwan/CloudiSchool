<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\School;
use Illuminate\Support\Str;

class DemoSchoolSeeder extends Seeder
{
    public function run(): void
    {
        $schoolName = 'Demo Public School';
        $school = School::where('schoolName', $schoolName)->first();

        if ($school) {
            $this->command->info($schoolName . ' already exists.');
            return;
        }

        // Check if there is a tenant we can link to if needed, or create a placeholder
        // Usually SchoolsSeeder might rely on tenancy logic, but here we just create the School entry
        // The implementation plan says "checking by name, ignoring global count"

        School::create([
             'schoolName' => $schoolName,
             'schoolCity' => 'Karachi',
             'address' => 'Demo Address, Karachi',
             'schoolAdminName' => 'Demo Admin',
             'schoolAdminEmail' => 'demo@school.com',
             'schoolAdminPassword' => bcrypt('password'), // or generic password
             'status' => 'active',
             'tenant_id' => 'demo_school_tenant', // Placeholder tenant ID or link to existing
        ]);
        
        $this->command->info($schoolName . ' created successfully.');
    }
}
