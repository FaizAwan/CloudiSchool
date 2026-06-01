<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SchoolsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('schools')) {
            $this->command?->warn('schools table not found; skipping SchoolsSeeder');
            return;
        }

        $exists = DB::table('schools')->count();
        if ($exists > 0) {
            $this->command?->info('Schools already present; skipping default school.');
            return;
        }

        DB::table('schools')->insert([
            'schoolName' => 'Demo Public School',
            'schoolCity' => 'Karachi',
            'address' => 'Main Street, Karachi, Pakistan',
            'schoolAdminName' => 'Super Admin',
            'schoolAdminEmail' => 'admin@example.com',
            'schoolAdminPassword' => bcrypt('password'),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command?->info('Default school created.');
    }
}