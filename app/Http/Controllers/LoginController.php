<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

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

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = md5($request->password);
        $user->phone = $request->phone;

        $user->save();
    }

    public function login_post(Request $request) {
        $name = $request->name;
        $password = $request->password;

        $user = User::where('name', $name)
            ->select('password', 'name')
            ->first();

        if(!$user) {
            return array('state'=>-2);
        }
        elseif($user->password == md5($password)) {
            session(['user' => $user->name]);
            return array('state'=>1);
        }
        else {
            return array('state'=>-1);
        }
    }
}
