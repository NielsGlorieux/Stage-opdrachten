<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePollTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
       Schema::create('polls', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('user_id');
            $table->integer('category_id');
            // $table->integer('score');
            $table->boolean('haveComments');
            $table->integer('maxVotes');
            $table->integer('maxLevelComments');
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
        Schema::table('polls', function (Blueprint $table) {
            //
        });
    }
}
