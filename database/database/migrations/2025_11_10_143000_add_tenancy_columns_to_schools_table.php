<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('schools')) {
            return; // central may not be using schools table
        }

        Schema::table('schools', function (Blueprint $table) {
            if (!Schema::hasColumn('schools', 'tenant_id')) {
                $table->string('tenant_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('schools', 'domain')) {
                $table->string('domain')->nullable()->after('tenant_id');
            }
            if (!Schema::hasColumn('schools', 'database_name')) {
                $table->string('database_name')->nullable()->after('domain');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('schools')) {
            return;
        }

        Schema::table('schools', function (Blueprint $table) {
            if (Schema::hasColumn('schools', 'tenant_id')) {
                $table->dropColumn('tenant_id');
            }
            if (Schema::hasColumn('schools', 'domain')) {
                $table->dropColumn('domain');
            }
            if (Schema::hasColumn('schools', 'database_name')) {
                $table->dropColumn('database_name');
            }
        });
    }
};
