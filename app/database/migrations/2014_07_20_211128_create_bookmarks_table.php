<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookmarksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
	    Schema::create('fs_bookmarks', function($table)
	    {
	        $table->bigIncrements('id')->unsigned();
	        $table->bigInteger('wp_user_id')->unsigned();
	        $table->bigInteger('recipe_id')->unsigned();
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
    	Schema::drop('fs_bookmarks');		
	}

}
