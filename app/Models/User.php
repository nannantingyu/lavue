<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
class User extends Authenticatable {
    protected $table = 'users';
    protected $fillable = ['name', 'nickname', 'password', 'phone', 'email', 'state'];

    public function roles() {
        return $this->belongsToMany('App\Models\Role', 'admin_role_user', 'user_id', 'role_id');
    }

    public function modulePermission($module) {
        $roles = $this->roles;
        $permissions = 0;
        foreach ($roles as $role) {
            $permissions = $permissions | $role->modulePermission($module);
        }

        return $permissions;
    }

    public function canRead($module) {
        return ($this->modulePermission($module) & 1) === 1;
    }

    public function canUpdate($module) {
        return ($this->modulePermission($module) & 2) === 2;
    }

    public function canDelete($module) {
        return ($this->modulePermission($module) & 4) === 4;
    }
}