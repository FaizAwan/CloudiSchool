<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('manual_exam_entries') && ! Schema::hasColumn('manual_exam_entries', 'tenant_id')) {
            Schema::table('manual_exam_entries', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable()->index()->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('manual_exam_entries') && Schema::hasColumn('manual_exam_entries', 'tenant_id')) {
            Schema::table('manual_exam_entries', function (Blueprint $table) {
                try { $table->dropIndex(['tenant_id']); } catch (\Throwable $e) {}
                $table->dropColumn('tenant_id');
            });
        }
    }
};
