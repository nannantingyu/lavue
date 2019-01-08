<?php

namespace App\Http\Controllers\Api;


use Captcha;
use Illuminate\Http\Request;
use Mrgoon\AliSms\AliSms;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{

    /**
     * 检查手机号码
     * @param $phone
     * @return bool
     */
    private function checkphone($phone)
    {
        preg_match('/^(13|14|15|17|18)\d{9}$/', $phone, $match);
        return !empty($match);
    }

    private function checkphoneExist($phone)
    {
        $phone_in_db = DB::table('site_user')->where('phone', $phone)->count();
        if ($phone_in_db > 0) {
            return true;
        }
        return false;
    }

    /**
     * 获取验证码
     * @param Request $request
     * @return mixed 图片验证码
     */
    public function getCaptcha(Request $request)
    {
        session()->remove('captcachecked');
        $img = Captcha::create('default', true);
        return $img['img'];
    }

    /**
     * 获取短信验证码
     * @return mixed|\SimpleXMLElement
     */
    private function sms($phone)
    {
        $aliSms = new AliSms();
        $code = random_int(1000, 9999);
        $response = $aliSms->sendSms($phone, 'SMS_135460074', ['code' => $code]);
        if ($response->Message != 'OK') {
            return ['success' => -1];
        }
        return ['success' => 1, 'code' => $code];
    }

    /**
     * 获取短信验证码
     * @param Request $request
     * @return array
     */
    public function getSms(Request $request)
    {
        $phone = $request->input('phone');
        $status = $request->input('status');
        $status = is_null($status) ? 1 : $status;
        $status = (int)$status;
        //status == 1 注册 否者找回密码

        $match = $this->checkphone($phone);

        if (empty($match)) {
            return ['success' => -1, 'msg' => '手机号不正确'];
        }

        $rules = ['cap' => 'required|captcha'];
        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['success' => 0, 'msg' => '图片验证码不正确'];
        }

        if ($status == 1 && $this->checkphoneExist($phone)) {
            return ['success' => 0, 'msg' => '手机号已经注册'];
        } elseif ($status != 1 && !$this->checkphoneExist($phone)) {
            return ['success' => 0, 'msg' => '手机号码未注册'];
        }

        $sms = $this->sms($phone);
        if ($sms['success'] !== 1) {
            return ['success' => -2, 'msg' => '短信验证码发送失败'];
        }
        $code = $sms['code'];
        session([
            'sms' => $code,
            'phone' => $phone,
            'sms_time' => time(),
            'status' => $status,
        ]);

        DB::table('site_sms')->insert([
            'phone' => $phone,
            'sms' => $code
        ]);
        session(['captcachecked' => true]);
        return ['success' => 1, 'msg' => "验证码已发送"];
    }

    protected function registerValidator(Request $request)
    {
        $messages = [
            'phone.required' => '手机号码不能为空',
            'phone.regex' => '手机号码不正确',
            'code.required' => '验证码不能为空',
            'pwd.required' => '密码不能为空',
            'pwd.max' => '密码必须大于等于8位并且小于16位',
            'pwd.min' => '密码必须大于等于8位并且小于16位',
        ];

        $rules = [
            'phone' => ['required', 'regex:/^(13|14|15|17|18|19)\d{9}$/'],
            'code' => 'required',
            'pwd' => 'required|max:16|min:8',
        ];

        return \Validator::make($request->all(), $rules, $messages);
    }


    //注册
    public function register(Request $request)
    {
        $validator = $this->registerValidator($request);

        if ($validator->fails()) {
            return ['success' => 0, 'msg' => $validator->errors()];
        }

        $sessionId = session()->getId();
        $phone = $request->input('phone');
        $code = $request->input('code');
        $pwd = $request->input('pwd');

        if ($this->checkphoneExist($phone)) {
            return ['success' => 0, 'msg' => '手机号已经注册'];
        }

        //从session判断是否是上次验证的号码
        if (session()->get('phone') != $phone
            || !session()->get('captcachecked')
        ) {
            return ['success' => -1, 'msg' => '未验证手机号码'];
        }

        $sms_code = session('sms');
        $sms_time = session('sms_time');
        $time_now = time();

        if (!$sms_code) {
            return ['success' => -1, 'msg' => '请先获取短信验证码'];
        } elseif ($time_now - $sms_time > 30 * 60) {
            session()->remove('sms');
            session()->remove('sms_time');
            return ['success' => -1, 'msg' => '验证码已过期，请重新获取'];
        } elseif ($code != session('sms')) {
            return ['success' => -1, 'msg' => '短信验证码不正确'];
        }

        //注册
        DB::table('site_user')->insert([
            'phone' => $phone,
            'password' => $pwd,
            'token' => $sessionId,
        ]);

        session()->remove('captcachecked');

        session(['status' => 1, 'phone' => $phone]);

        return $this->loginStatus($request);
    }

    protected function loginValidator(Request $request)
    {
        $messages = [
            'phone.required' => '手机号码不能为空',
            'pwd.required' => '密码不能为空',
        ];

        $rules = [
            'phone' => 'required',
            'pwd' => 'required',
        ];

        return \Validator::make($request->all(), $rules, $messages);
    }

    /**
     * 用户登录
     * @param Request $request
     * @return array
     */
    public function login(Request $request)
    {
        $validator = $this->loginValidator($request);

        if ($validator->fails()) {
            return ['success' => 0, 'msg' => $validator->errors()];
        }

        $sessionId = session()->getId();
        $phone = $request->input('phone');
        $pwd = $request->input('pwd');

        $exists = DB::table('site_user')
            ->where('phone', '=', $phone)
            ->where('password', '=', $pwd)
            ->exists();

        if ($exists) {
            //登录成功
            DB::table('site_user', $phone)
                ->where('phone', $phone)
                ->where('password', $pwd)
                ->update([
                    'token' => $sessionId,
                ]);

            session(['status' => 1, 'phone' => $phone]);

            return $this->loginStatus($request);
        } else {
            return ['success' => 0, 'msg' => '账号密码错误'];
        }
    }

    /**
     * 短信验证
     * @param Request $request
     * @return array
     */
    public function validatorAction(Request $request)
    {
        $messages = [
            'phone.required' => '手机号码不能为空',
            'phone.regex' => '手机号码不正确',
            'code.required' => '验证码不能为空',
        ];

        $rules = [
            'phone' => ['required', 'regex:/^(13|14|15|17|18|19)\d{9}$/'],
            'code' => 'required',
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return ['success' => 0, 'msg' => $validator->errors()];
        }

        $sessionId = session()->getId();
        $phone = $request->input('phone');
        $code = $request->input('code');

        if (!$this->checkphoneExist($phone)) {
            return ['success' => 0, 'msg' => '手机号未注册'];
        }

        //从session判断是否是上次验证的号码
        if (session()->get('phone') != $phone
            || !session()->get('captcachecked')
        ) {
            return ['success' => -1, 'msg' => '未验证手机号码'];
        }

        $sms_code = session('sms');
        $sms_time = session('sms_time');
        $time_now = time();

        if (!$sms_code) {
            return ['success' => -1, 'msg' => '请先获取短信验证码'];
        } elseif ($time_now - $sms_time > 30 * 60) {
            session()->remove('sms');
            session()->remove('sms_time');
            return ['success' => -1, 'msg' => '验证码已过期，请重新获取'];
        } elseif ($code != session('sms')) {
            return ['success' => -1, 'msg' => '短信验证码不正确'];
        }

        session([
            'phone' => $phone,
            'time' => time(),
            'status' => 0,
            'validator' => true
        ]);

        return ['success' => 1, 'msg' => '短信验证成功'];
    }


    public function forgotPwd(Request $request)
    {

        $messages = [
            'pwd.required' => '密码不能为空',
            'pwd.max' => '密码必须大于等于8位并且小于16位',
            'pwd.min' => '密码必须大于等于8位并且小于16位',
        ];

        $rules = [
            'pwd' => 'required|max:16|min:8',
        ];
        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return ['success' => 0, 'msg' => $validator->errors()];
        }

        $sessionId = session()->getId();
        $phone = session()->get('phone');
        $pwd = $request->input('pwd');

        if (!$this->checkphoneExist($phone)) {
            return ['success' => 0, 'msg' => '手机号未注册'];
        }

        //从session判断是否是上次验证的号码
        if (!$phone || !session()->get('validator')
        ) {
            return ['success' => -1, 'msg' => '未验证手机号码'];
        }

        $sms_time = session('time');
        $time_now = time();

        if ($time_now - $sms_time > 30 * 60) {
            session()->remove('time');
            session()->remove('time');
            return ['success' => -1, 'msg' => '当前操作已超时'];
        }

        //找回密码
        DB::table('site_user')
            ->where('phone', '=', $phone)
            ->update([
                'password' => $pwd,
                'token' => $sessionId,
            ]);

        session()->remove('captcachecked');

        session(['status' => 1, 'phone' => $phone]);

        return $this->loginStatus($request);
    }

    public function loginStatus(Request $request)
    {
        $status = session()->get('status');
        $phone = session()->get('phone');

        if (is_null($status) || is_null($phone)) {
            return ['success' => 0, 'msg' => '没有登录'];
        }

        $new_tel3 = preg_replace('/(\d{3})\d{4}(\d{4})/', '$1****$2', $phone);
        return ['success' => 1,
            'data' => [
                'phone' => $new_tel3,
                'token' => session()->getId()
            ],
            'msg' => '登录成功'];
    }

    public function logout(Request $request)
    {
        session()->remove('status');
        session()->remove('phone');
        return ['success' => 1,
            'msg' => '退出成功'];
    }

}
