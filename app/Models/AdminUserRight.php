<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_type_id
 * @property int $page_id
 * @property AdminGroupPage $adminGroupPage
 * @property AdminUserType $adminUserType
 */
class AdminUserRight extends Model
{
    protected $table = TBL_ADMIN_USER_RIGHT;
    
    /**
     * @var array
     */
    protected $fillable = ['user_type_id', 'page_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adminGroupPage()
    {
        return $this->belongsTo('App\Models\AdminGroupPage', 'page_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adminUserType()
    {
        return $this->belongsTo('App\Models\AdminUserType', 'user_type_id');
    }
}
