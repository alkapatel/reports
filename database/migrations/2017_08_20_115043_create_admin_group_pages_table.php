<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdminGroupPagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('admin_group_pages', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('admin_group_id')->index('admin_group_id');
			$table->string('name', 128);
			$table->string('menu_title', 128);
			$table->integer('menu_order');
			$table->text('description', 65535);
			$table->char('is_sub_menu', 1)->default('Y');
			$table->string('url', 512)->default('');
			$table->char('show_in_menu', 1)->default('Y');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('admin_group_pages');
	}

}
