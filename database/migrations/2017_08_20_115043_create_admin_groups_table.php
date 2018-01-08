<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdminGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('admin_groups', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('parent_id')->nullable()->index('parent_id');
			$table->string('title', 128);
			$table->integer('order_index')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('admin_groups');
	}

}
