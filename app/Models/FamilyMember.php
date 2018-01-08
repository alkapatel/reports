<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    public $timestamps = true;
    protected $table = TBL_FAMILY_MEMBER;
    
    protected $fillable = ['name','member_id','blood_group_id','relation_with_primary_member','occupation','image'];
}
