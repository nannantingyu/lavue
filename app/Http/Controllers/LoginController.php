<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function index(Request $request) {
        return view('login.index');
    }

    public function register(Request $request) {
        return view('login.register');
    }

    public function logout(Request $request) {
        $request->session()->forget('user');
        return redirect('/');
    }

    public function register_post(Request $request) {
        $rules = ['cap' => 'required|captcha'];
        $validator = \Validator::make(\Input::all(), $rules);

        if ($validator->fails())
        {
            return array('msg'=>'验证码不正确');
        }

        $user_in_tb = DB::table('users')->where('name', $request->name)
            ->orWhere('email', $request->email)
            ->orWhere('phone', $request->phone)
            ->get();

        if(!is_null($user_in_tb) and count($user_in_tb) > 0) {
            foreach($user_in_tb as $val) {
                if($val->name == $request->name) {
                    return ['msg'=>'名称已存在!'];
                }
                elseif($val->email == $request->email) {
                    return ['msg'=>'邮箱已注册!'];
                }
                elseif($val->phone == $request->phone) {
                    return ['msg'=>'手机号已注册!'];
                }
            }
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = md5($request->password);
        $user->phone = $request->phone;
        $user->state = 0;
        $user->check_str = str_random(40);

        \Mail::send('email.register', ['user' => $user], function ($m) use ($user) {
            $m->to($user->email, $user->name)->subject('注册邮件!');
        });

        $user->save();
    }

    public function login_post(Request $request) {
        $name = $request->name;
        $password = $request->password;

        $user = User::where('name', $name)
            ->select('password', 'name', 'state', 'email')
            ->first();

        if($user->state == 0){
            $user->email_link = $this->get_email_login_link($user->email);
        }

        if(!$user) {
            return array('state'=>-2);
        }
        elseif($user->password == md5($password)) {
            session(['user' => $user]);
            return array('state'=>1);
        }
        else {
            return array('state'=>-1);
        }
    }

    private function get_email_login_link($email) {
        $link = '/';
        if (strpos($email, 'qq') !== false) {
            $link = 'https://mail.qq.com';
        }
        else if (strpos($email, '163') !== false) {
            $link = 'http://mail.163.com/';
        }
        else if (strpos($email, '126') !== false) {
            $link = 'http://mail.126.com/';
        }
        else if (strpos($email, 'hotmail') !== false) {
            $link = 'https://outlook.live.com/';
        }
        else if (strpos($email, 'gmail') !== false) {
            $link = 'https://accounts.google.com/signin/v2/identifier';
        }
        else if (strpos($email, 'sina') !== false) {
            $link = 'http://mail.sina.com.cn/';
        }

        return $link;
    }

    public function check(Request $request){
        $check_str = $request->check_str;

        $user = $request->session()->get('user');
        if($user and $check_str) {
            $user_check = DB::table('users')->where('name', $user->name)
                ->select('id', 'name', 'check_str', 'email', 'state', 'email')
                ->first();

            if ($user_check and $user_check->check_str == $check_str) {
                User::where('name', $user->name)
                    ->update(['state'=>1]);

                $user->$user_check = $this->get_email_login_link($user->email);
                session(['user' => $user_check]);
                return view('login.check', ['check'=>true]);
            }
            else {
                return view('login.check', ['check'=>false, 'msg'=>'您的地址无效']);
            }
        }

        return view('login.check', ['check'=>false, 'msg'=>'请登录']);
    }
}
