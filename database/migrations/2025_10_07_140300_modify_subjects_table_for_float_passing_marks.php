<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Use raw SQL to modify column types to support decimal values if subjects table exists
        try {
            DB::statement('ALTER TABLE subjects MODIFY COLUMN passing_marks DECIMAL(8,3) NOT NULL DEFAULT 0');
        } catch (\Throwable $e) {}
        try {
            DB::statement('ALTER TABLE subjects MODIFY COLUMN total_marks DECIMAL(8,3) NOT NULL DEFAULT 0');
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        try { DB::statement('ALTER TABLE subjects MODIFY COLUMN passing_marks INT(11) NOT NULL DEFAULT 0'); } catch (\Throwable $e) {}
        try { DB::statement('ALTER TABLE subjects MODIFY COLUMN total_marks INT(11) NOT NULL DEFAULT 0'); } catch (\Throwable $e) {}
    }
};
