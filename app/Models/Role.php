<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {
    protected $table = 'admin_role';

    public function users() {
        return $this->belongsToMany('App\Models\User', 'admin_role_user', 'role_id', 'user_id');
    }

    public function modules() {
        return $this->belongsToMany('App\Models\Module', 'admin_module_permission', 'entity_id', 'module_id')
            ->withPivot('permission');
    }

    public function modulePermission($module) {
        $key = is_numeric($module)?'id': 'path';
        $module_permission = $this->modules->keyBy($key)->get($module, null);
        return is_null($module_permission) ? 0: $module_permission->pivot->permission;
    }
}