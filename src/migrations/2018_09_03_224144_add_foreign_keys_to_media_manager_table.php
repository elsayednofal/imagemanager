<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMediaManagerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('media_manager', function(Blueprint $table)
		{
			$table->foreign('cat_id', 'media_manager_ibfk_1')->references('id')->on('media_manager_cats')->onUpdate('SET NULL')->onDelete('SET NULL');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('media_manager', function(Blueprint $table)
		{
			$table->dropForeign('media_manager_ibfk_1');
		});
	}

}
