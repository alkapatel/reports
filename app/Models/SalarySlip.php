<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalarySlip extends Model
{
    public $timestamps = true;
    protected $table = TBL_SALARY_SLIP;

    /**
     * @var array
     */
    protected $fillable = ['user_id','ctc','month', 'year','account_num','joining_date','bank_name','working_days','designation','leave_taken','pan_num','basic_salary','advance','hra','leave_deduction','conveyance_allowance','other_deduction','telephone_allowance','tds', 'medical_allowance', 'uniform_allowance', 'special_allowance', 'bonus', 'arrear_salary','advance_given', 'leave_encashment', 'total_earning','total_deduction','net_pay','net_pay_words'];

	public static function listFilter($query)
    {
		$search_name = request()->get("search_name");
        $search_month = request()->get("search_month");
        $search_year = request()->get("search_year");
        if(!empty($search_name))
        {
            $query = $query->where(TBL_SALARY_SLIP.".user_id", $search_name);
        }
        if(!empty($search_month))
        {
            $query = $query->where(TBL_SALARY_SLIP.".month", $search_month);
        }
        if(!empty($search_year))
        {
            $query = $query->where(TBL_SALARY_SLIP.".year", $search_year);
        }
        return $query;        
    }
}
