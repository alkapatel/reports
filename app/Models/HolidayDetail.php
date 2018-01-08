<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HolidayDetail extends Model
{
    public $timestamps = true;
    protected $table = TBL_HOLIDAYS_DETAILS;
    /**
     * @var array
     */
    protected $fillable = ['holiday_id', 'date'];
}
