<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Change default role to 'admin' instead of 'superadmin'
        if (Schema::hasTable('users')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('role')->default('admin')->change();
                });
            } catch (\Throwable $e) {
                // Some DBs can't alter default this way; fallback to raw
                try { DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'admin'"); } catch (\Throwable $e2) {}
                try { DB::statement("ALTER TABLE users MODIFY role VARCHAR(255) NOT NULL DEFAULT 'admin'"); } catch (\Throwable $e3) {}
            }

            // Demote any tenant-bound users incorrectly set as superadmin
            DB::table('users')->whereNotNull('tenant_id')->where('role', 'superadmin')->update(['role' => 'admin']);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('role')->default('superadmin')->change();
                });
            } catch (\Throwable $e) {
                try { DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'superadmin'"); } catch (\Throwable $e2) {}
                try { DB::statement("ALTER TABLE users MODIFY role VARCHAR(255) NOT NULL DEFAULT 'superadmin'"); } catch (\Throwable $e3) {}
            }
        }
    }
};
