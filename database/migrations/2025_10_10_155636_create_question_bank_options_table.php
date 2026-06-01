<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('question_bank_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->string('option_letter', 5); // A, B, C, D, etc.
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
            
            $table->foreign('question_id')->references('id')->on('question_bank')->onDelete('cascade');
            $table->index(['question_id', 'is_correct']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_bank_options');
    }
};

