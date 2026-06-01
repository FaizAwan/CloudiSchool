<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('subjects')) { return; }

        Schema::table('subjects', function (Blueprint $table) {
            // Drop old global unique on subject_code if present
            try {
                $table->dropUnique('subjects_subject_code_unique');
            } catch (\Throwable $e) {
                try { $table->dropUnique(['subject_code']); } catch (\Throwable $e2) {}
            }

            // Add composite unique per tenant
            if (Schema::hasColumn('subjects', 'tenant_id')) {
                try { $table->unique(['tenant_id','subject_code'], 'subjects_tenant_code_unique'); } catch (\Throwable $e) {}
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('subjects')) { return; }

        Schema::table('subjects', function (Blueprint $table) {
            // Drop composite unique if present
            try { $table->dropUnique('subjects_tenant_code_unique'); } catch (\Throwable $e) {
                try { $table->dropUnique(['tenant_id','subject_code']); } catch (\Throwable $e2) {}
            }

            // Restore global unique on subject_code
            try { $table->unique('subject_code', 'subjects_subject_code_unique'); } catch (\Throwable $e) {}
        });
    }
};

