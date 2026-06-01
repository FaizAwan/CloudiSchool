<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('class_id');
            $table->string('section')->nullable();
            $table->string('studentName');
            $table->string('status')->default('active');
            $table->unsignedBigInteger('parent_id')->nullable()->index('students_parent_id_foreign');
            $table->string('grno')->nullable()->unique();
            $table->unsignedBigInteger('school_id')->nullable()->index('students_school_id_foreign');
            $table->string('session')->default('2024-2025');
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index('students_user_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
