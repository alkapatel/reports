<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $admin_group_id
 * @property string $name
 * @property string $menu_title
 * @property int $menu_order
 * @property string $description
 * @property string $is_sub_menu
 * @property string $url
 * @property string $show_in_menu
 * @property AdminGroup $adminGroup
 * @property AdminUserType[] $adminUserTypes
 */
class AdminGroupPage extends Model {
    public $timestamps = false;
    protected $table = TBL_ADMIN_GROUP_PAGE;

    /**
     * @var array
     */
    protected $fillable = ['id','admin_group_id', 'name', 'menu_title', 'menu_order', 'description', 'is_sub_menu', 'url', 'show_in_menu'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adminGroup() {
        return $this->belongsTo('App\Models\AdminGroup');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function adminUserTypes() {
        return $this->belongsToMany('App\Models\AdminUserType', TBL_ADMIN_USER_RIGHT, 'page_id', 'user_type_id');
    }

    public function GetPageGroup() {
        $result = \DB::table(TBL_ADMIN_GROUP)
                ->select(\DB::raw("title,id"))
                ->pluck('title', 'id')
                ->toArray();
        
        $resultArray = array();
        
        if ($result) {
            $resultArray = json_decode(json_encode($result), true);
        }
        
        return $resultArray;
    }

    public function GetAdminUser() {
        $result = \DB::table(TBL_USER_TYPES)
                ->select(\DB::raw("title,id"))
                ->pluck('title','id')
                ->toArray();

        $resultArray = array();

        if($result){
            $resultArray = json_decode(json_encode($result), true);
        }

        return $resultArray;
    }

}
