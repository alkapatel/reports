<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSentLog extends Model
{
    public $timestamps = true;
    protected $table = TBL_EMAIL_SENT_LOG;

    /**
     * @var array
     */
    protected $fillable = ['to_email', 'cc_emails', 'bcc_emails', 'from_email', 'email_subject', 'email_body','mail_response','status','ip_address','is_mandrill'];

}
