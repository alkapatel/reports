<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    public $timestamps = true;
    protected $table = TBL_HOLIDAYS;
    /**
     * @var array
     */
    protected $fillable = ['from_date', 'to_date', 'holiday_title', 'status','holidays_details'];

    
}
