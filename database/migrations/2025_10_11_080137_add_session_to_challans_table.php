<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSessionToChallansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('challans', function (Blueprint $table) {
            // Add session field that the ChallansController is looking for
            $table->string('session')->default('March 2024 to March 2025')->after('school_id');
            // Also add some other fields that might be needed
            $table->string('due_date')->nullable()->after('session');
            $table->string('month_name')->nullable()->after('due_date');
            $table->string('grno')->nullable()->after('month_name');
            $table->decimal('total_fee', 10, 2)->default(0)->after('grno');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('challans', function (Blueprint $table) {
            $table->dropColumn([
                'session',
                'due_date', 
                'month_name',
                'grno',
                'total_fee'
            ]);
        });
    }
}
