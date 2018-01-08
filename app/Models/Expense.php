<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
	protected $fillable = ['title','date','amount','scanned_bill','description_bill'];
    public $timestamps = true;
    protected $table = TBL_EXPENSES;

    public static function listFilter($query)
    {
        $search_start_date = request()->get("search_start_date");
        $search_end_date = request()->get("search_end_date");
        $search_title = request()->get("search_title");
        $search_amount = request()->get("search_amount");
            

        if (!empty($search_start_date)) {

            $from_date = $search_start_date . ' 00:00:00';
            $convertFromDate = $from_date;

            $query = $query->where(TBL_EXPENSES . ".created_at", ">=", addslashes($convertFromDate));
        }
        if (!empty($search_end_date)) {

            $to_date = $search_end_date . ' 23:59:59';
            $convertToDate = $to_date;

            $query = $query->where(TBL_EXPENSES . ".created_at", "<=", addslashes($convertToDate));
        }      
        if(!empty($search_title))
        {
            $query = $query->where("title", 'LIKE', '%'.$search_title.'%');
        }
        if (!empty($search_amount)) {
               $query = $query->where(TBL_EXPENSES.".amount", $search_hour_op, $search_amount);
        }  

        return $query;
    }

}
