<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class PurchaseNotificationRoleGroupContent extends Model
{
    protected $table = 'purchase_notification_roles_groups_contents';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function webinar()
    {
        return $this->belongsTo(Webinar::class, 'webinar_id', 'id');
    }

    public function bundle()
    {
        return $this->belongsTo(Bundle::class, 'bundle_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

}
