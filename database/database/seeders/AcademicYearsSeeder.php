<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicYearsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $years = [];
        
        // Generate academic years from 2025-2026 to 2098-2099
        for ($i = 2025; $i < 2099; $i++) {
            $years[] = [
                'academicYear' => $i . '-' . ($i + 1),
                'start_date' => $i . '-04-01',
                'end_date' => ($i + 1) . '-03-31',
                'is_active' => 'no',
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        // Insert in batches to avoid memory issues
        $chunks = array_chunk($years, 50);
        foreach ($chunks as $chunk) {
            DB::table('academicyears')->insert($chunk);
        }
        
        echo "Added academic years from 2025-2026 to 2098-2099\n";
    }
}
