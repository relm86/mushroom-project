<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecipesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
	    Schema::create('fs_recipes', function($table)
	    {
	        $table->bigIncrements('id')->unsigned();
	        $table->bigInteger('wp_user_id')->unsigned();
	        $table->string('title',255);
	        $table->LongText('recipe');
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
		//
    	Schema::drop('fs_recipes');		
	}

}
