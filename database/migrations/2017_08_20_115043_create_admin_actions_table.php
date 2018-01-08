<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdminActionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('admin_actions', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('description', 100)->nullable()->comment('Description of Admin Action.');
			$table->string('remark', 500)->nullable()->comment('Details Description of Admin Action.');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('admin_actions');
	}

}
