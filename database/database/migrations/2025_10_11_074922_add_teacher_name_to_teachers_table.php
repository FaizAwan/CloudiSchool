<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeacherNameToTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teachers', function (Blueprint $table) {
            // Add teacher_name field for timetable compatibility (duplicate of teacherName)
            $table->string('teacher_name')->nullable()->after('teacherName');
        });
        
        // Copy data from teacherName to teacher_name for existing records
        DB::statement('UPDATE teachers SET teacher_name = teacherName WHERE teacherName IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('teacher_name');
        });
    }
}
