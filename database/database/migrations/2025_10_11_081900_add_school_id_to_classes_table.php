<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSchoolIdToClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('classes', function (Blueprint $table) {
            // Add school_id field that the reports are expecting
            $table->unsignedBigInteger('school_id')->default(1)->after('className');
            $table->string('session')->default('April 2024 to March 2025')->after('school_id');
            $table->string('status')->default('active')->after('session');
            
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
        Schema::table('classes', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['school_id']);
            
            // Drop columns
            $table->dropColumn([
                'school_id',
                'session',
                'status'
            ]);
        });
    }
}
