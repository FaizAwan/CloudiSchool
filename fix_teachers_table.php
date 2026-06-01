<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Checking 'teachers' table...\n";
    if (Schema::hasTable('teachers')) {
        if (!Schema::hasColumn('teachers', 'status')) {
            echo "Adding 'status' column to 'teachers' table...\n";
            Schema::table('teachers', function (Blueprint $table) {
                $table->string('status')->default('active')->after('id');
            });
            echo "Column 'status' added successfully.\n";
        } else {
            echo "Column 'status' already exists in 'teachers' table.\n";
        }

        if (!Schema::hasColumn('teachers', 'teacherName')) {
            echo "Adding 'teacherName' column to 'teachers' table...\n";
            Schema::table('teachers', function (Blueprint $table) {
                $table->string('teacherName')->nullable()->after('id');
            });
            echo "Column 'teacherName' added successfully.\n";
        }
    } else {
        echo "Table 'teachers' does not exist.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
