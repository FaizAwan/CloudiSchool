<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('subjects') && ! Schema::hasColumn('subjects', 'tenant_id')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable()->index()->after('id');
            });
            // Best-effort backfill from classes.tenant_id if available
            try {
                if (Schema::hasTable('classes') && Schema::hasColumn('classes', 'tenant_id')) {
                    DB::statement('UPDATE subjects s JOIN classes c ON c.id = s.class_id SET s.tenant_id = c.tenant_id WHERE s.tenant_id IS NULL');
                }
            } catch (\Throwable $e) {
                // ignore if DB does not support the syntax
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('subjects') && Schema::hasColumn('subjects', 'tenant_id')) {
            Schema::table('subjects', function (Blueprint $table) {
                try { $table->dropIndex(['tenant_id']); } catch (\Throwable $e) {}
                $table->dropColumn('tenant_id');
            });
        }
    }
};
