<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAdminGroupPagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('admin_group_pages', function(Blueprint $table)
		{
			$table->foreign('admin_group_id', 'admin_group_pages_ibfk_1')->references('id')->on('admin_groups')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('admin_group_pages', function(Blueprint $table)
		{
			$table->dropForeign('admin_group_pages_ibfk_1');
		});
	}

}
