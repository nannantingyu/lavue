<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;
use App\Models\Module;

class ModuleController extends Controller
{
    /**
     * 获取模块
     * @auth module:read
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getModule(Request $request) {
        $modules = $this->getModuleList();
        $tree = $this->getModuleTree($modules);
        return response()->json(['success'=>1, 'data'=>$tree, 'list'=>$modules]);
    }

    /**
     * 添加或修改模块
     * @auth module:update
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function addOrUpdateModule(Request $request) {
        $messages = [
            'name.required' => '请输入模块名',
            'name.max' => '模块名长度不能超过32个字符',
            'name.min' => '模块名长度不能少于2个字符',
            'pid.required' => '父模块不能为空',
            'pid.integer' => '父模块不正确',
            'path.required' => '模块路径不能为空',
            'path.max' => '模块路径长度不超过64个字符',
            'state.required' => '状态不能为空',
            'state.integer' => '状态不正确',
            'sequence.required' => '顺序不能为空',
            'sequence.integer' => '顺序不正确',
        ];

        $rules = [
            'name' => 'required|max:32|min:2',
            'pid' => 'required|integer',
            'path' => 'required|max:64',
            'state' => 'required|integer',
            'sequence' => 'required|integer'
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);
        if($validator->fails()) {
            return response()->json(['success'=>0, "errors"=>$validator->errors()]);
        }

        $form = [
            'name' => $request->input('name'),
            'pid' => $request->input('pid'),
            'path' => $request->input('path'),
            'state' => $request->input('state'),
            'sequence' => $request->input('sequence'),
            'display' => $request->input('display'),
        ];

        $id = $request->input('id');
        if(is_null($id)) {
            $module = new Module($form);
            $module->save();
        }
        else {
            $module = Module::find($id);
            foreach ($form as $key=>$val) {
                $module->$key = $val;
            }

            $module->save();
        }

        return ['success'=>1, 'id'=>$module->id];
    }

    /**
     * 将数组转化为级属关系的树形结构
     * @param $list
     * @param $pid
     * @return array
     */
    private function list_to_tree($list, $pid, $parent_key='pid') {
        $tree = [];

        foreach ($list as $key=>$val) {
            if($val[$parent_key] == $pid) {
                $val['label'] = $val['name'];
                $val['id'] = $val['id'];
                $val['children'] = $this->list_to_tree($list, $val['id']);
                $tree[] = $val;
            }
        }

        return $tree;
    }

    /**
     * 获取模块列表
     * @return \Illuminate\Support\Collection
     */
    private function getModuleList() {
        $modules = Module::orderBy('pid', 'asc')
            ->orderBy('sequence', 'asc')
            ->get();

        $modules->prepend(
            new Module([
                'id'=>0,
                'pid'=>-1,
                'name'=>'根路径',
                'path'=>'/',
                'sequence'=>1,
                'state'=>1,
                'display'=>1
            ])
        );

        return $modules;
    }

    /**
     * 获取模块树形结构
     * @param null $modules
     * @return array
     */
    private function getModuleTree($modules=null) {
        if(is_null($modules)) {
            $modules = $this->getModuleList();
        }

        $tree = $this->list_to_tree(objToArray($modules), -1);
        return $tree;
    }
    /**
     * 获取模块
     * @auth module:read
     * @param Request $request
     */
    public function getModuleTreeSelect(Request $request) {
        $tree = $this->getModuleTree();
        $tree = $this->buildTreeSelect($tree);
        return response()->json(['success'=>1, 'data'=>$tree]);
    }

    /**
     * 计算树形结构每一个模块的深度
     * @param $tree
     * @param int $step
     * @return array
     */
    private function buildTreeSelect($tree, $step=0) {
        $lists = [];
        foreach ($tree as $key=>$val) {
            $lists[] = [
                'id'=>$val['id'],
                'name'=>$val['name'],
                'path'=>$val['path'],
                'step'=>$step
            ];

            $lists = array_merge($lists, $this->buildTreeSelect($val['children'], $step+1));
        }

        return $lists;
    }

    /**
     * 获取模块权限
     * @auth permission:read
     * @param Request $request
     */
    public function getModulePermission(Request $request) {
//        $module = Redis::get('admin_module');
        $module = null;
        if(!is_null($module)) {
            eval("\$module=$module;");
        }
        else {
            $module = objToArray($this->getModuleList());
            $module_var = var_export($module, true);
//            Redis::set('admin_module', $module_var);
        }

        $id = $request->input('id');

        if(!is_null($id) && \numcheck::is_int($id)) {
            $modulePermission = DB::table('admin_module_permission')
                ->where('admin_module_permission.entity_type', 'role')
                ->where('admin_module_permission.module_id', $id)
                ->join('admin_module', 'admin_module_permission.module_id', '=', 'admin_module.id')
                ->select('admin_module_permission.*', 'admin_module.name')
                ->get();

            $modulePermission = array_column(objToArray($modulePermission), null, 'entity_id');
            return ['success'=>1, 'data'=>$modulePermission];
        }

        return ['success'=>0, 'msg'=>'模块ID不正确', 'module'=>$module];
    }

    /**
     * 获取模块的所有的父级分类
     * @param $pid
     * @param $modules
     * @return array
     */
    private function getParents($pid, $modules) {
        $parents = [];
        foreach ($modules as $m) {
            if($m['id'] == $pid) {
                $parents[] = $m['id'];
                if($m['pid'] != 0)
                    $parents = array_merge($parents, $this->getParents($m['pid'], $modules));
            }
        }

        return $parents;
    }

    /**
     * 获取角色拥有的模块权限
     * @auth permission:read
     * @param Request $request
     * @return array
     */
    public function getRoleMoudlePermission(Request $request) {
        $role_id = $request->input('role_id');
        if(\numcheck::is_int($role_id)) {
            $modulePermission = DB::table('admin_module_permission')
                ->where('admin_module_permission.entity_type', 'role')
                ->where('admin_module_permission.entity_id', $role_id)
                ->join('admin_module', 'admin_module_permission.id', '=', 'admin_module.id')
                ->select('admin_module_permission.*', 'admin_module.name', 'admin_module.path')
                ->get();

            return ['success'=>1, 'data'=>$modulePermission];
        }

        return ['success'=>0, 'msg'=>'角色不存在'];
    }

    /**
     * 获取用户拥有的模块权限
     * @param Request $request
     * @return array
     */
    public function getUserModulePermission(Request $request) {
        $user_id = Auth::user()->id;
        $modulePermission = [];

        if($user_id === 1) {
            $modules = DB::table('admin_module')
                ->select('id', 'name', 'path')
                ->orderBy('sequence', 'asc')
                ->get();

            foreach ($modules as $key=>$val) {
                $modulePermission[$val->path]['permission'] = 7;
                $modulePermission[$val->path]['name'] = $val->name;
                $modulePermission[$val->path]['path'] = $val->path;
                $modulePermission[$val->path]['id'] = $val->id;
            }

            return ['success'=>1, 'data'=>$modulePermission];
        }

        if(\numcheck::is_int($user_id)) {
            $role_id = DB::table('admin_role_user')
                ->where('user_id', $user_id)
                ->pluck('role_id')
                ->toArray();

            $modulePermissionDb = DB::table('admin_module_permission')
                ->where('admin_module_permission.entity_type', 'role')
                ->whereIn('admin_module_permission.entity_id', $role_id)
                ->join('admin_module', 'admin_module_permission.module_id', '=', 'admin_module.id')
                ->select('admin_module_permission.*', 'admin_module.name', 'admin_module.path')
                ->get();


            //Merge Permision

            foreach ($modulePermissionDb as $permission) {
                if(isset($modulePermission[$permission->path])) {
                    $modulePermission[$permission->path]['permission'] =
                        $modulePermission[$permission->path]['permission'] | $permission->permission;
                }
                else {
                    $modulePermission[$permission->path] = [
                        'id'=>$permission->module_id,
                        'name'=>$permission->name,
                        'path'=>$permission->path,
                        'permission'=> $permission->permission,
                    ];
                }
            }

            return ['success'=>1, 'data'=>$modulePermission];
        }

        return ['success'=>0, 'msg'=>'用户不存在'];
    }

    /**
     * 给用户或者角色添加权限
     * @auth permission:update
     */
    public function addModulePermission(Request $request) {
        $messages = [
            'id.required' => '模块必须',
            'entity_id.required' => '角色或用户必须',
            'permission.required' => '权限必须',
        ];

        $rules = [
            'id' => 'required|integer',
            'entity_id' => 'required|integer',
            'entity_type' => 'required',
            'permission' => 'required|integer',
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);
        if($validator->fails()) {
            return response()->json(['success'=>0, "errors"=>$validator->errors()]);
        }

        $entity_type = $request->input('entity_type');
        if($entity_type != 'user' and $entity_type != 'role') {
            return ['success'=>0, 'msg'=>'权限角色类型不正确'];
        }

        $entity_id = $request->input('entity_id');
        $id = $request->input('id');

        if($this->checkHasSetedModulePermission($entity_type, $entity_id, $id)) {
            DB::table('admin_module_permission')
                ->where('entity_type', $entity_type)
                ->where('entity_id', $entity_id)
                ->where('module_id', $id)
                ->update([
                    'permission' => $request->input('permission'),
                ]);
        }
        else {
            DB::table('admin_module_permission')
                ->insert([
                    'entity_type' => $entity_type,
                    'entity_id' => $entity_id,
                    'module_id' => $id,
                    'permission' => $request->input('permission'),
                ]);
        }

        return ['success'=>1];
    }

    /**
     * 判断是否已经为某角色添加某模块的权限
     * @param $entity_type
     * @param $entity_id
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    private function checkHasSetedModulePermission($entity_type, $entity_id, $id) {
        $permission = DB::table('admin_module_permission')
            ->where('entity_type', $entity_type)
            ->where('entity_id', $entity_id)
            ->where('module_id', $id)
            ->first();

        return $permission;
    }
}