<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentsType extends Model
{
     public $timestamps = true;
    protected $table = TBL_DOCUMENTS_TYPE;
    /**
     * @var array
     */
    protected $fillable = ['id', 'title'];
}
