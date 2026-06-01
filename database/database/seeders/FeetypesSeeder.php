<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FeetypesSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('feetypes')) {
            $this->command?->warn('feetypes table not found; skipping FeetypesSeeder');
            return;
        }

        $types = [
            ['name' => 'Tuition Fee', 'description' => 'Monthly tuition fee', 'status' => 'active'],
            ['name' => 'Admission Fee', 'description' => 'One-time admission fee', 'status' => 'active'],
            ['name' => 'Examination Fee', 'description' => 'Exam and paper checking charges', 'status' => 'active'],
            ['name' => 'IT Fee', 'description' => 'Computer lab and IT services', 'status' => 'active'],
            ['name' => 'Security Fund', 'description' => 'Refundable security fund', 'status' => 'active'],
            ['name' => 'Lab Charges', 'description' => 'Science lab charges', 'status' => 'active'],
            ['name' => 'Library Fee', 'description' => 'Library services fee', 'status' => 'active'],
            ['name' => 'Sports Fund', 'description' => 'Sports and physical activities', 'status' => 'active'],
            ['name' => 'Development Fund', 'description' => 'School development fund', 'status' => 'active'],
            ['name' => 'Miscellaneous', 'description' => 'Miscellaneous charges', 'status' => 'active'],
        ];

        foreach ($types as $t) {
            DB::table('feetypes')->updateOrInsert(
                ['name' => $t['name']],
                array_merge($t, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $this->command?->info('Fee types seeded.');
    }
}