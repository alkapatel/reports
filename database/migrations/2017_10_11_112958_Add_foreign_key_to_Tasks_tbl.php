<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToTasksTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Tasks', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')
                    ->on('users')
                    ->onUpdate('RESTRICT')
                    ->onDelete('RESTRICT');
            $table->foreign('project_id')->references('id')
                    ->on('projects')
                    ->onUpdate('RESTRICT')
                    ->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Tasks', function (Blueprint $table) {
            //
        });
    }
}
