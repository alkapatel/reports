<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailSentLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_sent_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('to_email')->nullable();
            $table->string('cc_emails')->nullable();
            $table->string('bcc_emails')->nullable();
            $table->string('from_email')->nullable();
            $table->string('email_subject')->nullable();
            $table->text('email_body')->nullable();
            $table->string('mail_response')->nullable();
            $table->string('status')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('is_mandrill')->nullable();
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
        Schema::dropIfExists('email_sent_logs');
    }
}
