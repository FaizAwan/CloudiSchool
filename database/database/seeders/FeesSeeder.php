<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FeesSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('fees')) {
            $this->command?->warn('fees table not found; skipping FeesSeeder');
            return;
        }

        // Ensure some feetypes exist
        (new FeetypesSeeder())->run();

        $feetypes = DB::table('feetypes')->pluck('id', 'name')->toArray();
        $classes = DB::table('classes')->pluck('className', 'id')->toArray();
        if (empty($classes)) {
            $this->command?->warn('No classes found; cannot seed fees.');
            return;
        }

        $months = ['April','May','June','July','August','September','October','November','December','January','February','March'];
        $year = 2025;

        $existing = DB::table('fees')->count();
        $target = 20;
        $toInsert = max(0, $target - (int)$existing);
        $rows = [];
        $i = 0;
        foreach ($classes as $classId => $className) {
            foreach ($feetypes as $ftName => $ftId) {
                if ($i >= $toInsert) break 2;
                $month = $months[$i % count($months)];
                $amount = rand(200, 2000);
                $rows[] = [
                    'student_id' => null,
                    'class_id' => $classId,
                    'class_name' => $className,
                    'fee_description' => $ftName,
                    'amount' => $amount,
'status' => 'unpaid',
                    'month' => $month,
                    'month_name' => $month,
                    'year' => $year,
                    'fee_type_id' => $ftId,
                    'fee_value' => $amount,
                    'session' => '2024-2025',
                    'school_id' => 1,
                    'fee_name' => $ftName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $i++;
            }
        }

        if ($rows) {
            DB::table('fees')->insert($rows);
            $this->command?->info('Fees seeded: ' . count($rows));
        } else {
            $this->command?->info('Fees already have >= 20 entries; skipping.');
        }
    }
}