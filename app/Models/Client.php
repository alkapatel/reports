<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    public $timestamps = true;
    protected $table = TBL_CLIENT;

    /**
     * @var array
     */
    protected $fillable = ['name', 'email', 'phone', 'country', 'state', 'city','send_email'];
	
	function getClients($onlyIDS = 0)
    {
        $id = $this->id;

        $query = \DB::table(TBL_CLIENT_EMPLOYEE)
                ->where("client_id",$id)
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
