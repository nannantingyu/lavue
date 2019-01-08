<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller {
    use RegistersUsers {
        register as authenticatesUsersRegister;
    }

    public function __construct() {
//        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $messages = [
            'name.required' => '请输入用户名',
            'name.max' => '用户名长度不能超过32个字符',
            'name.min' => '用户名长度不能少于3个字符',
            'nickname.required' => '请输入昵称',
            'nickname.max' => '昵称长度不能超过32个字符',
            'nickname.min' => '昵称长度不能少于2个字符',
            'email.required' => '请输入邮箱地址',
            'email.E-Mail' => '邮箱地址不正确',
            'password.required' => '密码不能为空',
            'password.min' => '密码不能少于6个字符',
            'password.max' => '密码不能多于32个字符',
            'phone.required' => '手机号不能为空',
            'phone.regex' => '手机号格式不正确',
        ];

        $rules = [
            'name' => 'required|max:32|min:3',
            'nickname' => 'required|max:32|min:2',
            'email' => 'required|E-Mail',
            'password' => 'required|min:6|max:32',
            'phone' => ['required', 'regex:/^(13|14|15|17|18|19)\d{9}$/'],
        ];

        return Validator::make($data, $rules, $messages);
    }

    /**
     * 后台注册
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function regist(Request $request) {
        $this->validator($request->all())->validate();
        $user_exist = $this->checkUserExists($request->all());
        if($user_exist) {
            return response()->json(['success'=>0, 'errors'=>['name'=>[$user_exist.'已存在']]]);
        }

        event(new Registered($user = $this->create($request->all())));
        $this->guard()->login($user);
        return ['success'=>1, 'userid'=>$user->id, 'nickname'=>$user->nickname];
    }

    /**
     * 判断用户是否已经注册
     * @param $user
     * @param $id
     * @return bool|string
     */
    private function checkUserExists($user) {
        $user_in_db = User::where(function ($query) use($user) {
                $query->where('name', $user['name'])
                    ->orWhere('phone', $user['phone'])
                    ->orWhere('email', $user['email']);
            });

        if(!is_null($user['id'])) {
            $user_in_db = $user_in_db->where('id', '!=', $user['id']);
        }

        $user_in_db = $user_in_db->first();
        if(is_null($user_in_db)) return false;
        elseif($user_in_db->name == $user['name']) return '用户名';
        elseif($user_in_db->phone == $user['phone']) return '手机号';
        else return '邮箱';
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'nickname' => $data['nickname'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);
    }
}