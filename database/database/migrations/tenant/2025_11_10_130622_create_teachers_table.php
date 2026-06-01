<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('teacherName')->nullable();
            $table->string('teacher_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('class_id')->nullable()->index('teachers_class_id_foreign');
            $table->string('className')->nullable();
            $table->unsignedBigInteger('school_id')->nullable()->index('teachers_school_id_foreign');
            $table->string('status')->default('active');
            $table->unsignedBigInteger('user_id')->nullable()->index('teachers_user_id_foreign');
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
        Schema::dropIfExists('teachers');
    }
}
