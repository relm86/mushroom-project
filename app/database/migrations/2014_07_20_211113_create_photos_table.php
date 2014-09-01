<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhotosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
	    Schema::create('fs_photos', function($table)
	    {
	        $table->bigIncrements('id')->unsigned();
	        $table->bigInteger('wp_user_id')->unsigned();
	        $table->bigInteger('recipe_id')->unsigned();
	        $table->integer('img_order')->unsigned();
	        $table->string('img_path',255);
	        $table->integer('img_width');
	        $table->integer('img_height');
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
    	Schema::drop('fs_photos');		
	}

}
