<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToQuestionBankOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('question_bank_options', function (Blueprint $table) {
            $table->foreign(['question_id'])->references(['id'])->on('question_bank')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('question_bank_options', function (Blueprint $table) {
            $table->dropForeign('question_bank_options_question_id_foreign');
        });
    }
}
