<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    protected $redirectTo = '/admin#/';
    use AuthenticatesUsers {
        login as authenticatesUsersLogin;
    }

    /**
     * 登录
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request) {
        $request->merge([
            $this->username() => $request->input('username'),
        ]);

        $result = $this->authenticatesUsersLogin($request);
        if(!isset($result->message)) {
            $user = Auth::user();
            if($user->state != 1) {
                $this->guard()->logout();
                return response(["data"=>['_token'=>$request->session()->token()], "errors"=>["state"=>["该用户已被禁用，请联系管理员！"]]], 422);
            }

            return ['success'=>1, 'userid'=>$user->id, 'nickname'=>$user->nickname, '_token'=>$request->session()->token()];
        }
        else {
            return $result;
        }
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function username()
    {
        return username(
            request()->input('username')
        );
    }

    /**
     * 修改用户密码
     *
     * @auth user:edit
     * @param Request $request
     */
    public function setPassword(Request $request) {
        $userid = $request->input('userid');
        $password = $request->input('password');
        $password = Hash::make($password);

        $user = Auth::user();
        if($user->id === 1) {
            User::where('id', $userid)->update([
                'password'=>$password
            ]);

            return ['success'=>1, 'user'=>$user, 'password'=>$password];
        }

        return ['success'=>0, 'msg'=>'无权修改密码'];
    }

    /**
     * 退出登录
     */
    public function adlogout(Request $request) {
        $this->guard()->logout();
        $request->session()->invalidate();
        return ['success'=>1, 'session'=>$request->session()->all()];
    }

    /**
     * 添加角色
     *
     * @auth add-role:update
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function addRole(Request $request) {
        $messages = [
            'role_name.required' => '请输入角色名',
            'role_name.max' => '角色名长度不能超过32个字符',
            'role_name.min' => '角色名长度不能少于3个字符',
            'state.integer' => '状态不正确'
        ];

        $rules = [
            'role_name' => 'required|max:32|min:3',
            'state' => 'integer',
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);
        if($validator->fails()) {
            return response()->json(['success'=>0, "errors"=>$validator->errors()]);
        }

        $role_name = $request->input('role_name');
        $state = $request->input('state');

        $form = [
            'role_name' => $role_name,
            'state' => $state,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $role_id = $request->input('id');

        //判断角色是否存在
        if($this->checkRoleExists($role_name, $role_id)) {
            return ['success'=>0, 'msg'=>'角色名已存在'];
        }

        if(!is_null($role_id) and is_int($role_id)) {
            DB::table('admin_role')->where('id', $role_id)
                ->update($form);
        }
        else {
            $form['created_at'] = date('Y-m-d H:i:s');
            DB::table('admin_role')->insert($form);
        }

        return ['success'=>1];
    }

    /**
     * 判断角色是否已经存在
     * @param $role_name
     * @param $role_id
     * @return bool|string
     */
    private function checkRoleExists($role_name, $role_id) {
        $role_in_db = DB::table('admin_role')
            ->where('role_name', $role_name);

        if(!is_null($role_id)) {
            $role_in_db = $role_in_db->where('id', '!=', $role_id);
        }

        $role_in_db = $role_in_db->first();
        if(is_null($role_in_db)) return false;
        return true;
    }

    /**
     * 获取所有的角色
     *
     * @auth role-user:read
     * @param Request $request
     */
    public function getRoles(Request $request) {
        $roles = DB::table('admin_role')
            ->orderBy('created_at', 'asc')
            ->get();

        return ['success'=>1, 'data'=>$roles];
    }

    /**
     * 获取所有的用户
     *
     * @auth user:read
     * @param Request $request
     */
    public function getUsers(Request $request) {
        $users = User::orderBy('updated_at', 'desc')
            ->select('id', 'name', 'nickname', 'phone', 'state', 'email', 'created_at', 'updated_at')
            ->get();

        return ['success'=>1, 'data'=>$users];
    }

    /**
     * 获取角色下所有的用户
     *
     * @auth role-user:read
     * @param Request $request
     */
    public function getRoleUsers(Request $request) {
        $role_id = $request->input('role_id');
        if(!is_null($role_id)) {
            $roles = DB::table('users')
                ->join('admin_role_user', 'users.id', '=', 'admin_role_user.user_id')
                ->where('admin_role_user.role_id', $role_id)
                ->orderBy('users.updated_at', 'desc')
                ->select('users.id', 'users.name', 'users.nickname')
                ->get();
        }

        return ['success'=>1, 'data'=>$roles];
    }

    /**
     * 为用户添加角色
     *
     * @auth role-user:update
     * @param Request $request
     */
    public function assignRoleForUser(Request $request) {
        $role_id = $request->input('role_id');
        $users = $request->input('users');

        if(is_null($users) or !is_int($role_id)) {
            return ['success'=>0, '信息不正确'];
        }

        $has_roles = [];
        $add_users = [];
        $users = explode(',', $users);
        foreach($users as $user_id) {
            if($this->hasRole($user_id, $role_id)) {
                $has_roles[] = $user_id;
            }
            else {
                $add_users[] = [
                    'role_id'=>$role_id,
                    'user_id'=>$user_id
                ];
            }
        }

        if(!empty($add_users)) {
            DB::table('admin_role_user')->insert($add_users);
        }

        if(!empty($has_roles)) {
            return ['success'=>0, '用户已经拥有该角色', 'users'=>$has_roles];
        }

        return ['success'=>1];
    }

    /**
     * 收回用户的角色
     * @auth role-user:update
     * @param Request $request
     */
    public function retractRoleFromUser(Request $request) {
        $role_id = $request->input('role_id');
        $users = $request->input('users');

        if(is_null($users) or !is_int($role_id)) {
            return ['success'=>0, '信息不正确'];
        }

        DB::table('admin_role_user')
            ->where('role_id', $role_id)
            ->whereIn('user_id', explode(',', $users))
            ->delete();

        return ['success'=>1];
    }

    /**
     * @auth role-user:read
     * 判断用户是否含有某个角色
     */
    public function hasRole($user_id, $role_id) {
        $user_role = DB::table('admin_role_user')
            ->where('role_id', $role_id)
            ->where('user_id', $user_id)
            ->first();

        return !is_null($user_role);
    }

    /**
     * 更新用户信息
     * @param Request $request
     */
    public function addOrUpdateUser(Request $request) {
        //验证表单
        $validate = $this->validateForm($request);
        if (!$validate['success']) {
            return response()->json(['success' => 0, "errors" => $validate['msg']]);
        }

        $form = [
            'name' => $username = $request->input('name'),
            'nickname' => $username = $request->input('nickname'),
            'email' => $username = $request->input('email'),
            'phone' => $username = $request->input('phone'),
        ];

        User::find($request->input('id'))->update($form);
        return ['success'=>1];
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateForm(Request $request)
    {
        $messages = [
            'id.required' => '请输入用户ID',
            'id.integer' => '请输入正确的用户ID',
            'state.required' => '请输入正确的状态',
            'state.integer' => '请输入正确的状态',
            'name.required' => '请输入用户名',
            'name.max' => '用户名长度不能超过32个字符',
            'name.min' => '用户名长度不能少于3个字符',
            'nickname.required' => '请输入昵称',
            'nickname.max' => '昵称长度不能超过32个字符',
            'nickname.min' => '昵称长度不能少于2个字符',
            'email.required' => '请输入邮箱地址',
            'email.E-Mail' => '邮箱地址不正确',
            'phone.required' => '手机号不能为空',
            'phone.regex' => '手机号格式不正确',
        ];

        $rules = [
            'id' => 'required|integer',
            'state' => 'required|integer',
            'name' => 'required|max:32|min:3',
            'nickname' => 'required|max:32|min:2',
            'email' => 'required|E-Mail',
            'phone' => ['required', 'regex:/^(13|14|15|17|18|19)\d{9}$/'],
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return ['success' => 0, 'msg' => $validator->errors()];
        }

        return ['success' => 1];
    }

    /**
     * 设置用户状态
     * @param Request $request
     * @return array
     */
    public function setState(Request $request)
    {
        $id = $request->input('id');
        $state = $request->input('state');

        if(\numcheck::is_int($id) and \numcheck::is_int($state)) {
            User::where('id', $id)
                ->update(['state' => $state]);

            return ['success' => 1];
        }

        return ['success' => 0];
    }
}