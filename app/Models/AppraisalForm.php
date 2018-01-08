<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppraisalForm extends Model
{
    public $timestamps = true;
    protected $table = TBL_APPRAISAL_FORM;

    /**
     * @var array
     */
    protected $fillable = ['user_id','past_year_rate','past_year_achieved','job_satisfaction','achievements','goal','duty_responsibility','suggestion','current_salary','expected_salary','raise','is_submit','years','months','submited_at'];
}
