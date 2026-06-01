<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // In tenant DB, there's no central 'schools' table. Skip FK if not present.
        if (Schema::hasTable('schools')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->foreign(['school_id'])->references(['id'])->on('schools')->onDelete('CASCADE');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign('classes_school_id_foreign');
        });
    }
}
