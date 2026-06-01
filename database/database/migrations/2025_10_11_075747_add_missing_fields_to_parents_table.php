<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingFieldsToParentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parents', function (Blueprint $table) {
            // Add missing fields needed by ChallansController and other parts of the system
            $table->string('fatherName')->nullable()->after('parentName');
            $table->string('motherName')->nullable()->after('fatherName');
            $table->string('phone')->nullable()->after('motherName');
            $table->string('email')->nullable()->after('phone');
            $table->text('address')->nullable()->after('email');
            $table->string('occupation')->nullable()->after('address');
            $table->enum('is_commandercityschool_employee', ['Yes', 'No'])->default('No')->after('occupation');
            $table->string('status')->default('active')->after('is_commandercityschool_employee');
            $table->unsignedBigInteger('school_id')->nullable()->after('status');
            
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
        Schema::table('parents', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['school_id']);
            
            // Drop columns
            $table->dropColumn([
                'fatherName',
                'motherName',
                'phone',
                'email',
                'address',
                'occupation',
                'is_commandercityschool_employee',
                'status',
                'school_id'
            ]);
        });
    }
}
