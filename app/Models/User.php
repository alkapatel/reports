<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Cviebrock\EloquentSluggable\Sluggable;

class User extends Model
{
    public $timestamps = true;
    use Notifiable, Sluggable;
    protected $table = TBL_USERS;
   

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['firstname','lastname','email','user_type_id','address','phone','password','name','status','image','joining_date','blood_group','ifsc_code','bank_nm','account_no','account_nm','dob','pan_num','adhar_num','designation','is_add_task','is_show_appraisal_form'];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
   /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => array('firstname','lastname'),
                'on_update' => true
            ]
        ];
    }
    public function Task()
    {
        return $this->hasMany('App\Models\Task');
    }

    public function LeaveRequest()
    {
        return $this->hasMany('App\Models\LeaveRequest');
    }

    public static function getList(){

        $users = User::orderby('name')->pluck("name","id")->all();
        return $users;
    }
    public static function getAdminEmails()
    {
        return self::
        where("user_type_id",ADMIN_USER_TYPE)
        ->pluck("email")
        ->toArray();
        
    }
	public static function getUserDob()
    {
        $dobList = User::select('dob','name')->where('dob','!=',null)->get()->toArray();
        return $dobList;
    }

    
}
