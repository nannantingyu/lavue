<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\WxUser;

class WxController extends Controller
{

    /**
     * 添加或者更新微信用户
     * @param Request $request
     */
    public function addOrUpdateWxUser(Request $request) {
        $validator = $this->wxUserValidator($request);
        if ($validator->fails()) {
            return ['success' => 0, 'msg' => $validator->errors()];
        }

        $wx_id = $request->input('wx_id');
        $form = [
            'wx_name' => $request->input('wx_name'),
            'avatar' => $request->input('avatar'),
            'wx_id' => $wx_id,
            'country' => $request->input('country'),
            'province' => $request->input('province'),
            'city' => $request->input('city'),
            'gender' => $request->input('gender'),
        ];

        $user = WxUser::where('wx_id', $wx_id)->first();

        if (!is_null($user)) {
            WxUser::where('id', $user->id)->update($form);
        } else {
            $info = new WxUser($form);
            $info->save();
            $id = $info->id;
        }
        return ['success' => 1, 'data' => ['id' => $id]];
    }

    /**
     * 获取用户信息
     * @param Request $request
     */
    public function getUserInfo(Request $request) {
        $wx_id = $request->get('wx_id');
        if(is_null($wx_id)) {
            return ['success'=>0, 'message'=>'参数不正确'];
        }

        $user = WxUser::where("wx_id", $wx_id)->first();
        if(is_null($user)) {
            return ['success'=>0, 'message'=>'用户未注册'];
        }

        return ['success'=>1, 'data'=>$user];
    }

    /**
     * 账单类型表单验证
     * @param $request
     * @return mixed
     */
    private function wxUserValidator($request)
    {
        $messages = [
            'wx_name.required' => '请输入账单类型',
            'wx_name.max' => '账单类型长度不能超过64个字符',
            'wx_name.min' => '账单类型长度不能少于2个字符',
            'avatar.required' => '请输入账单描述',
            'avatar.max' => '账单描述长度不能超过255个字符',
            'avatar.min' => '账单描述长度不能少于2个字符',
            'wx_id.required' => '微信id不能为空',
            'gender.required' => '性别不能为空',
            'country.required' => '国家不能为空',
        ];

        $rules = [
            'wx_name' => 'required|max:64|min:2',
            'avatar' => 'required|max:255|min:2',
            'wx_id' => 'required',
            'gender' => 'required|integer',
            'country' => 'required',
        ];
        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * @param Request $request
     */
    public function getWxOpenid(Request $request) {
        $code = $request->input('code');
        if(!is_null($code)) {
            $appid = "wx8e4e12d6284850a5";
            $secret = "4f30b527ab7a9c1f19c34146dece52f7";
            $openid_url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$secret&js_code=$code&grant_type=authorization_code";
            $data = https_request($openid_url);
            return ['success'=>1, "data"=>$data];
        }

        return ['success'=>0];
    }
}
