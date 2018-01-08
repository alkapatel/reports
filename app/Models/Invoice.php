<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    public $timestamps = true;
    protected $table = TBL_INVOICE;
     
    protected $fillable = ['to_address', 'invoice_no', 'invoice_date', 'cgst_amount','sgst_amount','total_amount','total_amount_words','address','pan_no','gst_regn_no','bank_account_no','bank_name','bank_swift_code','ifsc_code','require_gst','currency','client_id','payment'];
	
	public static function listFilter($query){
    	
    	$search_start_date = request()->get("search_start_date");
        $search_end_date = request()->get("search_end_date");
        $search_invoice_no = request()->get("search_invoice_no");

        if (!empty($search_start_date))
        {
            $from_date = $search_start_date . ' 00:00:00';
            $convertFromDate = $from_date;

            $query = $query->where("created_at", ">=", addslashes($convertFromDate));
        }
        if (!empty($search_end_date)) {

            $to_date = $search_end_date . ' 23:59:59';
            $convertToDate = $to_date;

            $query = $query->where("created_at", "<=", addslashes($convertToDate));
        }
        if(!empty($search_invoice_no))
        {
            $query = $query->where('invoice_no', 'LIKE', '%'.$search_invoice_no.'%');
        	 
        }
        return $query;
    }
}
