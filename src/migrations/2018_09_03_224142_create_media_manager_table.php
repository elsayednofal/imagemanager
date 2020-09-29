<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMediaManagerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('media_manager', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 191)->unique('image_manager_name_unique');
			$table->string('content', 191);
			$table->string('alt', 191);
			$table->integer('cat_id')->nullable()->index('cat_id');
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
		Schema::drop('media_manager');
	}

}
