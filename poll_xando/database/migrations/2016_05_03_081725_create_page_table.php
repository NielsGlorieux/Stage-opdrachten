<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug');
            $table->string('title');
            $table->text('content');
            $table->boolean('isForm');
            $table->boolean('isStandard');//Dit maakt het onderscheid tussen de pagina's die standaard aanwezig zijn en de nieuw aangemaakt pagina's door admins. Op deze manier kunnen de juiste links gebruikt worden in het navigatie menu. Zie pages in admin dashboard.
            $table->timestamps();
        });
    
    DB::table('pages')->insert(
    array(
        'slug' => 'home',
        'title' => 'Home',
        'content'=>'',
        'isForm'=>'0',
        'isStandard' => '1'
    ));
    DB::table('pages')->insert(
    array(
        'slug' => 'polls',
        'title' => 'All polls',
        'content'=>'',
        'isForm'=>'0',
        'isStandard' => '1'
    ));
    DB::table('pages')->insert(
    array(
        'slug' => 'poll/create',
        'title' => 'New poll',
        'content'=>'',
        'isForm'=>'0',
        'isStandard' => '1'
    ));
    DB::table('pages')->insert(
    array(
        'slug' => 'forum',
        'title' => 'Forum',
        'content'=>'',
        'isForm'=>'0',
        'isStandard' => '1'
    ));
    DB::table('pages')->insert(
    array(
        'slug' => 'inbox',
        'title' => 'Inbox',
        'content'=>'',
        'isForm'=>'0',
        'isStandard' => '1'
    ));
 
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pages');
    }
}
