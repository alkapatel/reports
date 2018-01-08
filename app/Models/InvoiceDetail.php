<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    public $timestamps = true;
    protected $table = TBL_INVOICE_DETAIL;
    /**
     * @var array
     */
    protected $fillable = ['invoice_id', 'particular', 'amount'];
}
