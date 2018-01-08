<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credential extends Model
{
    public $timestamps = true;
    protected $table = TBL_CREDENTIAL;

    /**
     * @var array
     */
    protected $fillable = ['project_id', 'protocol','port','hostname','username','password','private_key','url','key_file','description','environment','key_file_password','created_by','title'];

    public function Project()
    {
        return $this->hasMany('App\Models\Project');
    }
	function getUsers($onlyIDS = 0)
    {
        $id = $this->id;

        $query = \DB::table(TBL_SHARE_USER)
                ->where("credential_id",$id)
                ->get();

        if($onlyIDS == 1)
        {
            $arr = array();
            foreach($query as $row)
            {   
                $arr[] = $row->user_id;
            }
            return $arr;
        }        
        else
        {
            return $query;        
        }             
        
    }
}
