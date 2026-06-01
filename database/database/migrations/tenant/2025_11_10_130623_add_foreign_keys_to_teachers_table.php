<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('schools')) {
            Schema::table('teachers', function (Blueprint $table) {
                $table->foreign(['school_id'])->references(['id'])->on('schools')->onDelete('CASCADE');
            });
        }
        Schema::table('teachers', function (Blueprint $table) {
            $table->foreign(['class_id'])->references(['id'])->on('classes')->onDelete('SET NULL');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeign('teachers_school_id_foreign');
            $table->dropForeign('teachers_class_id_foreign');
            $table->dropForeign('teachers_user_id_foreign');
        });
    }
}
