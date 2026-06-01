<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_attendances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('teacher_id');
            $table->date('date')->index();
            $table->enum('status', ['present', 'absent', 'leave', 'late'])->default('present')->index();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('school_id')->default('1');
            $table->unsignedBigInteger('marked_by')->index('teacher_attendances_marked_by_foreign');
            $table->timestamps();

            $table->unique(['teacher_id', 'date'], 'teacher_attendance_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teacher_attendances');
    }
}
