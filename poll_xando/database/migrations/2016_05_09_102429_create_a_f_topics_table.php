<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAFTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
 public function up()
    {
        Schema::create('a_f_topics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject');
            $table->text('content');
            $table->integer('user_id');
            $table->integer('a_f_category_id');
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
        Schema::drop('a_f_topics');
    }
}
