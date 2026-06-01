<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            if (!Schema::hasColumn('schools', 'bank_name')) {
                $table->string('bank_name')->nullable();
            }
            if (!Schema::hasColumn('schools', 'bank_branch')) {
                $table->string('bank_branch')->nullable();
            }
            if (!Schema::hasColumn('schools', 'bank_account_title')) {
                $table->string('bank_account_title')->nullable();
            }
            if (!Schema::hasColumn('schools', 'bank_account_number')) {
                $table->string('bank_account_number')->nullable();
            }
            if (!Schema::hasColumn('schools', 'bank_iban')) {
                $table->string('bank_iban')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            if (Schema::hasColumn('schools', 'bank_name')) {
                $table->dropColumn('bank_name');
            }
            if (Schema::hasColumn('schools', 'bank_branch')) {
                $table->dropColumn('bank_branch');
            }
            if (Schema::hasColumn('schools', 'bank_account_title')) {
                $table->dropColumn('bank_account_title');
            }
            if (Schema::hasColumn('schools', 'bank_account_number')) {
                $table->dropColumn('bank_account_number');
            }
            if (Schema::hasColumn('schools', 'bank_iban')) {
                $table->dropColumn('bank_iban');
            }
        });
    }
};
