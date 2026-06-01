<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('students')) {
            return;
        }

        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'grno')) {
                return;
            }

            // Drop old global unique index on grno if it exists
            try {
                $table->dropUnique('students_grno_unique');
            } catch (\Throwable $e) {
                try {
                    $table->dropUnique(['grno']);
                } catch (\Throwable $e2) {
                    // ignore if it doesn't exist
                }
            }

            // Add composite unique index per tenant if tenant_id column exists
            if (Schema::hasColumn('students', 'tenant_id')) {
                try {
                    $table->unique(['tenant_id', 'grno'], 'students_tenant_grno_unique');
                } catch (\Throwable $e) {
                    // ignore if it already exists
                }
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('students')) {
            return;
        }

        Schema::table('students', function (Blueprint $table) {
            // Drop composite unique if present
            try {
                $table->dropUnique('students_tenant_grno_unique');
            } catch (\Throwable $e) {
                try { $table->dropUnique(['tenant_id', 'grno']); } catch (\Throwable $e2) {}
            }

            // Restore global unique on grno if column exists
            if (Schema::hasColumn('students', 'grno')) {
                try {
                    $table->unique('grno', 'students_grno_unique');
                } catch (\Throwable $e) {}
            }
        });
    }
};
