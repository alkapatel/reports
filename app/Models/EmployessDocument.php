<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployessDocument extends Model
{
    public $timestamps = true;
    protected $table = TBL_EMPLOYESS_DOCUMENTS;
    /**
     * @var array
     */
    protected $fillable = ['id','user_id', 'doc_type_id', 'filename'];
}
