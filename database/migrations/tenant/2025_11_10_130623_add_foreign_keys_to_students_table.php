<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Skip school FK in tenant DB; parents/users FKs are fine
        if (Schema::hasTable('schools')) {
            Schema::table('students', function (Blueprint $table) {
                $table->foreign(['school_id'])->references(['id'])->on('schools')->onDelete('CASCADE');
            });
        }
        Schema::table('students', function (Blueprint $table) {
            $table->foreign(['parent_id'])->references(['id'])->on('parents')->onDelete('SET NULL');
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
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign('students_school_id_foreign');
            $table->dropForeign('students_parent_id_foreign');
            $table->dropForeign('students_user_id_foreign');
        });
    }
}
