<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrincipalRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('principal_remarks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('percentage_min', 5)->default(0);
            $table->decimal('percentage_max', 5)->default(100);
            $table->text('remark');
            $table->tinyInteger('is_active')->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['percentage_min', 'percentage_max']);
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('principal_remarks');
    }
}
