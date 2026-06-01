<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingFieldsToStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            // Add missing fields needed by ChallansController and other parts of the system
            $table->string('status')->default('active')->after('studentName');
            $table->unsignedBigInteger('parent_id')->nullable()->after('status');
            $table->string('grno')->unique()->nullable()->after('parent_id'); // GR Number
            $table->unsignedBigInteger('school_id')->nullable()->after('grno');
            $table->string('session')->default('2024-2025')->after('school_id');
            $table->string('gender')->nullable()->after('session');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->text('address')->nullable()->after('date_of_birth');
            $table->string('phone')->nullable()->after('address');
            $table->string('email')->nullable()->after('phone');
            $table->unsignedBigInteger('user_id')->nullable()->after('email');
            
            // Add foreign key constraints
            $table->foreign('parent_id')->references('id')->on('parents')->onDelete('set null');
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
        Schema::table('students', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['school_id']);
            $table->dropForeign(['user_id']);
            
            // Drop columns
            $table->dropColumn([
                'status',
                'parent_id',
                'grno',
                'school_id',
                'session',
                'gender',
                'date_of_birth',
                'address',
                'phone',
                'email',
                'user_id'
            ]);
        });
    }
}
