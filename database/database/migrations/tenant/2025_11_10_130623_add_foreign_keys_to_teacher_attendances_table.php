<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTeacherAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teacher_attendances', function (Blueprint $table) {
            $table->foreign(['teacher_id'])->references(['id'])->on('teachers')->onDelete('CASCADE');
            $table->foreign(['marked_by'])->references(['id'])->on('users')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teacher_attendances', function (Blueprint $table) {
            $table->dropForeign('teacher_attendances_teacher_id_foreign');
            $table->dropForeign('teacher_attendances_marked_by_foreign');
        });
    }
}
