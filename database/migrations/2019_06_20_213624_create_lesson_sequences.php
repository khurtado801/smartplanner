<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLessonSequences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_sequences', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lesson_delivery');
            $table->string('activity');
            $table->string('description');
            $table->string('benefits');
            $table->string('bloom');
            $table->integer('webb');
            $table->string('modifications');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lesson_sequences');
    }
}
