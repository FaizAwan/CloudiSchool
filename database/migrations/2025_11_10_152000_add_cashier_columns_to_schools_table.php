<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('schools')) {
            return;
        }

        Schema::table('schools', function (Blueprint $table) {
            if (!Schema::hasColumn('schools', 'stripe_id')) {
                $table->string('stripe_id')->nullable()->index()->after('database_name');
            }
            if (!Schema::hasColumn('schools', 'pm_type')) {
                $table->string('pm_type')->nullable()->after('stripe_id');
            }
            if (!Schema::hasColumn('schools', 'pm_last_four')) {
                $table->string('pm_last_four', 4)->nullable()->after('pm_type');
            }
            if (!Schema::hasColumn('schools', 'trial_ends_at')) {
                $table->timestamp('trial_ends_at')->nullable()->after('pm_last_four');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('schools')) {
            return;
        }

        Schema::table('schools', function (Blueprint $table) {
            foreach (['stripe_id', 'pm_type', 'pm_last_four', 'trial_ends_at'] as $col) {
                if (Schema::hasColumn('schools', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
