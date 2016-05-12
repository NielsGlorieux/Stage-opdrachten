<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('value');
            $table->timestamps();
        });
        
        DB::table('settings')->insert(
        array(
            'name' => 'percentages',
            'value' => 'true'
        ));
        DB::table('settings')->insert(
        array(
            'name' => 'theme',
            'value' => ''
        ));
        DB::table('settings')->insert(
        array(
            'name' => 'completedPollLook',
            'value' => '0'
        ));
           DB::table('settings')->insert(
        array(
            'name' => 'navOrder',
            'value' => '["1","2","3","4"]'
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
}
