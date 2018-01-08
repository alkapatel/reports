<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public $timestamps = true;
    protected $table = TBL_PROJECT;

    /**
     * @var array
     */
    protected $fillable = ['id','title', 'status','client_id'];

    public function Client()
    {
        return $this->hasMany('App\Models\Client');
    }

    public static function getList(){

        $users = Project::orderby('title')->pluck("title","id")->all();
        return $users;
    }
	public static function getProjectList($client_type){

        $projects = Project::where('status',1)->where('client_id',$client_type)->orderby('title')->pluck("title","id")->all();
        return $projects;
    }
}
