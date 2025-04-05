<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $timestamps = false;
    static $admin = 'admin';
    static $user = 'user';
    static $teacher = 'teacher';
    static $organization = 'organization';

    protected $guarded = ['id'];

    public function canDelete()
    {
        switch ($this->name) {
            case self::$admin:
            case self::$user:
            case self::$organization:
            case self::$teacher:
                return false;
                break;
            default:
                return true;
        }
    }

    public function users()
    {
        return $this->hasMany('App\User', 'role_id', 'id');
    }

    public function isDefaultRole()
    {
        return in_array($this->name, [self::$admin, self::$user, self::$organization, self::$teacher]);
    }

    public function isMainAdminRole()
    {
        return $this->name == self::$admin;
    }

    public static function getUserRoleId()
    {
        $id = 1; // user role id

        $role = self::where('name', self::$user)->first();

        return !empty($role) ? $role->id : $id;
    }

    public static function getTeacherRoleId()
    {
        $id = 4; // teacher role id

        $role = self::where('name', self::$teacher)->first();

        return !empty($role) ? $role->id : $id;
    }

    public static function getOrganizationRoleId()
    {
        $id = 3; // teacher role id

        $role = self::where('name', self::$organization)->first();

        return !empty($role) ? $role->id : $id;
    }
}
