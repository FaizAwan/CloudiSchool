<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_terms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('term_name')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->json('applicable_classes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();

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
        Schema::dropIfExists('exam_terms');
    }
}
