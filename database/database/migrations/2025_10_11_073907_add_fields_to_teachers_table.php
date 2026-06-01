<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teachers', function (Blueprint $table) {
            // Add missing fields based on TeachersController usage
            $table->string('teacherName')->nullable()->after('id');
            $table->string('email')->nullable()->after('teacherName');
            $table->string('phone')->nullable()->after('email');
            $table->unsignedBigInteger('class_id')->nullable()->after('phone');
            $table->string('className')->nullable()->after('class_id');
            $table->unsignedBigInteger('school_id')->nullable()->after('className');
            $table->string('status')->default('active')->after('school_id');
            $table->unsignedBigInteger('user_id')->nullable()->after('status');
            
            // Add foreign key constraints
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('set null');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teachers', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['class_id']);
            $table->dropForeign(['school_id']);
            $table->dropForeign(['user_id']);
            
            // Drop columns
            $table->dropColumn([
                'teacherName',
                'email',
                'phone',
                'class_id',
                'className',
                'school_id',
                'status',
                'user_id'
            ]);
        });
    }
}
