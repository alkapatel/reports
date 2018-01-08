<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAdminUserRightsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('admin_user_rights', function(Blueprint $table)
		{
			$table->foreign('page_id', 'admin_user_rights_ibfk_1')->references('id')->on('admin_group_pages')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('user_type_id', 'admin_user_rights_ibfk_2')->references('id')->on('admin_user_types')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('admin_user_rights', function(Blueprint $table)
		{
			$table->dropForeign('admin_user_rights_ibfk_1');
			$table->dropForeign('admin_user_rights_ibfk_2');
		});
	}

}
