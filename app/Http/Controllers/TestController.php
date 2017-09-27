<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function index(Request $request) {
        $user = (object)array(
            'email' => '939259192@qq.com',
            'name'  => '小师傅的臭兔兔',
            'check_str' => 'abcdefghijklmnopqrstuvwxyz'
        );

        \Mail::send('email.register', ['user' => $user], function ($m) use ($user) {
            $m->to($user->email, $user->name)->subject('Your Reminder!');
        });

//        return "发送成功";
        return view('email/register', $user);
    }
}
