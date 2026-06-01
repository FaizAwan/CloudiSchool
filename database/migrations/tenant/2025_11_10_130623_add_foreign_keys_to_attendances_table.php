<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign(['created_by'])->references(['id'])->on('users')->onDelete('CASCADE');
            $table->foreign(['teacher_id'])->references(['id'])->on('users')->onDelete('SET NULL');
            $table->foreign(['class_id'])->references(['id'])->on('classes')->onDelete('CASCADE');
            $table->foreign(['student_id'])->references(['id'])->on('students')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign('attendances_created_by_foreign');
            $table->dropForeign('attendances_teacher_id_foreign');
            $table->dropForeign('attendances_class_id_foreign');
            $table->dropForeign('attendances_student_id_foreign');
        });
    }
}
