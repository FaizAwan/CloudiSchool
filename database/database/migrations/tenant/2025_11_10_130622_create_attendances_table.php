<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('class_id')->index('attendances_class_id_foreign');
            $table->date('date')->index();
            $table->string('session')->default('2024-2025')->index();
            $table->enum('status', ['present', 'absent', 'leave', 'late'])->default('present')->index();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('school_id')->default('1');
            $table->unsignedBigInteger('teacher_id')->nullable()->index('attendances_teacher_id_foreign');
            $table->unsignedBigInteger('created_by')->index('attendances_created_by_foreign');
            $table->timestamps();

            $table->unique(['student_id', 'class_id', 'date', 'session'], 'attendance_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
