<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertLeaveRequestDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leave_request_details', function (Blueprint $table) {
            $table->foreign('leave_id')->references('id')
                    ->on('leave_requests')
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
        Schema::table('leave_request_details', function (Blueprint $table) {
            //
        });
    }
}
