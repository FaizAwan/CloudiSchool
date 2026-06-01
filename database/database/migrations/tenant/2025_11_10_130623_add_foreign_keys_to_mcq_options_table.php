<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToMcqOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mcq_options', function (Blueprint $table) {
            $table->foreign(['question_id'])->references(['id'])->on('exam_questions')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mcq_options', function (Blueprint $table) {
            $table->dropForeign('mcq_options_question_id_foreign');
        });
    }
}
