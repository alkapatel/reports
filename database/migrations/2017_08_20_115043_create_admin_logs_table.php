<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdminLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('admin_logs', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->bigInteger('adminuserid')->default(0);
			$table->bigInteger('actionid')->default(0);
			$table->string('actionvalue', 30)->nullable();
			$table->text('remark')->nullable();
			$table->string('ipaddress', 50)->nullable();
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
		Schema::drop('admin_logs');
	}

}
