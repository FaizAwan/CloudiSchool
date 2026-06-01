<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSessionAndSchoolIdToFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fees', function (Blueprint $table) {
            // Add session field that the reports are expecting
            $table->string('session')->default('March 2024 to March 2025')->after('fee_value');
            // Also add school_id for multi-school support
            $table->unsignedBigInteger('school_id')->default(1)->after('session');
            // Add fee name for better organization
            $table->string('fee_name')->nullable()->after('school_id');
            
            // Add foreign key constraint
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fees', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['school_id']);
            
            // Drop columns
            $table->dropColumn([
                'session',
                'school_id',
                'fee_name'
            ]);
        });
    }
}
