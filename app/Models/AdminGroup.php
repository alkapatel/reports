<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $parent_id
 * @property string $title
 * @property int $order_index
 * @property AdminGroup $adminGroup
 * @property AdminGroupPage[] $adminGroupPages
 */
class AdminGroup extends Model
{
    public $timestamps = false;
    protected $table = TBL_ADMIN_GROUP;
    /**
     * @var array
     */
    protected $fillable = ['parent_id', 'title', 'order_index'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adminGroup()
    {
        return $this->belongsTo('App\Models\AdminGroup', 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adminGroupPages()
    {
        return $this->hasMany('App\Models\AdminGroupPage');
    }
}
