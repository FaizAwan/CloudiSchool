<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('max_students')->default(0)->after('price');
            $table->integer('max_staff')->default(0)->after('max_students');
            $table->string('max_storage_size')->default('1GB')->after('max_staff'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['max_students', 'max_staff', 'max_storage_size']);
        });
    }
};
