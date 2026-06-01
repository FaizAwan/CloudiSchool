<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAbsentSubjectsToManualExamEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manual_exam_entries', function (Blueprint $table) {
            $table->json('absent_subjects')->nullable()->after('data')->comment('JSON array of subjects where student was absent');
            $table->json('subjects_to_improve')->nullable()->after('absent_subjects')->comment('JSON array of subjects requiring improvement');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manual_exam_entries', function (Blueprint $table) {
            $table->dropColumn(['absent_subjects', 'subjects_to_improve']);
        });
    }
}
