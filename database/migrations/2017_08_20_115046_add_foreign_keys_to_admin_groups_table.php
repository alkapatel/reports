<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAdminGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('admin_groups', function(Blueprint $table)
		{
			$table->foreign('parent_id', 'admin_groups_ibfk_1')->references('id')->on('admin_groups')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('admin_groups', function(Blueprint $table)
		{
			$table->dropForeign('admin_groups_ibfk_1');
		});
	}

}
