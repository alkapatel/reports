<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('user_type_id')->unsigned()->nullable();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('address')->nullable();
            $table->integer('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('slug')->nullable();
            $table->timestamp('last_login_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->string('remember_token')->nullable();
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
		Schema::drop('users');
	}

}
