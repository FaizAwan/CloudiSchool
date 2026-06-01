<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('schools')) {
            return;
        }

        Schema::table('schools', function (Blueprint $table) {
            if (! Schema::hasColumn('schools', 'created_by_user_id')) {
                $table->unsignedBigInteger('created_by_user_id')->nullable()->after('id');

                // Optional FK to users table (guarded with try/catch in case of driver quirks)
                try {
                    $table->foreign('created_by_user_id')
                          ->references('id')
                          ->on('users')
                          ->onDelete('set null');
                } catch (\Throwable $e) {
                    // If FK fails (duplicate name, etc.), we silently ignore; column still exists.
                }
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('schools')) {
            return;
        }

        Schema::table('schools', function (Blueprint $table) {
            if (Schema::hasColumn('schools', 'created_by_user_id')) {
                try {
                    $table->dropForeign(['created_by_user_id']);
                } catch (\Throwable $e) {}
                $table->dropColumn('created_by_user_id');
            }
        });
    }
};
