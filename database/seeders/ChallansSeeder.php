<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChallansSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('challans')) {
            $this->command?->warn('challans table not found; skipping ChallansSeeder');
            return;
        }

        $students = DB::table('students')->limit(50)->get();
        if ($students->isEmpty()) {
            // If no students, skip seeding challans for now
            $this->command?->warn('No students found; skipping ChallansSeeder');
            return;
        }

        $existing = DB::table('challans')->count();
        $target = 20;
        $toMake = max(0, $target - (int)$existing);
        if ($toMake <= 0) {
            $this->command?->info('Challans already have >= 20 entries; skipping.');
            return;
        }

        $rows = [];
        for ($i = 1; $i <= $toMake; $i++) {
            $student = $students->random();
            $amount = rand(1000, 5000);
            $rows[] = [
                'student_id' => $student->id,
                'student_name' => $student->studentName,
                'class_name' => DB::table('classes')->where('id', $student->class_id)->value('className'),
                'challan_number' => 'CH' . str_pad((string)(DB::table('challans')->max('id') + $i), 6, '0', STR_PAD_LEFT),
                'amount' => $amount,
                'status' => 'generated',
                'paid' => (rand(0,1) ? 'YES' : 'NO'),
                'issued_date' => now()->toDateString(),
                'month' => 'April',
                'year' => 2025,
                'total' => $amount,
                'school_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('challans')->insert($rows);
        $this->command?->info('Challans seeded: ' . count($rows));
    }
}