<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\WxAccount;
use App\Models\WxAccountLog;
use App\Models\WxAccountType;

class AccountController extends Controller
{

    /**
     * 添加或者更新账单类型
     * @param Request $request
     */
    public function addOrUpdateAccountType(Request $request) {
        $validator = $this->accountTypeValidator($request);
        if ($validator->fails()) {
            return ['success' => 0, 'msg' => $validator->errors()];
        }
        $form = [
            'account_type' => $request->input('account_type'),
            'account_description' => $request->input('account_description'),
            'wx_id' => $request->input('wx_id'),
            'icon' => $request->input('icon')
        ];

        $id = $request->input('id');

        if (!is_null($id)) {
            WxAccountType::where('id', $id)->update($form);
        } else {
            $info = new WxAccountType($form);
            $info->save();
            $id = $info->id;
        }
        return ['success' => 1, 'data' => ['id' => $id]];
    }

    /**
     * 删除账单类型
     * @param Request $request
     */
    public function removeAccountType(Request $request) {
        $id = $request->input('id');
        $wx_id = $request->input('wx_id');
        if(!is_null($id)) {
            WxAccountType::where('id', $id)->where('wx_id', $wx_id)->delete();
            return ['success' => 1];
        }

        return ['success' => 0];
    }

    /**
     * 账单类型表单验证
     * @param $request
     * @return mixed
     */
    private function accountTypeValidator($request)
    {
        $messages = [
            'account_type.required' => '请输入账单类型',
            'account_type.max' => '账单类型长度不能超过64个字符',
            'account_type.min' => '账单类型长度不能少于2个字符',
            'account_description.required' => '请输入账单描述',
            'account_description.max' => '账单描述长度不能超过255个字符',
            'account_description.min' => '账单描述长度不能少于2个字符',
            'wx_id.required' => '微信id不能为空',
        ];

        $rules = [
            'account_type' => 'required|max:64|min:2',
            'account_description' => 'required|max:255|min:2',
            'wx_id' => 'required',
        ];
        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * 添加或者更新账单
     * @param Request $request
     */
    public function addOrUpdateAccount(Request $request) {
        $validator = $this->accountValidator($request);
        if ($validator->fails()) {
            return ['success' => 0, 'msg' => $validator->errors()];
        }
        $form = [
            'account_type' => $request->input('account_type'),
            'wx_id' => $request->input('wx_id'),
            'account_name' => $request->input('account_name'),
            'account_description' => $request->input('account_description'),
            'single_price' => $request->input('single_price'),
            'icon' => $request->input('icon')
        ];

        $id = $request->input('id');

        if (!is_null($id)) {
            WxAccount::where('id', $id)->update($form);
        } else {
            $info = new WxAccount($form);
            $info->save();
            $id = $info->id;
        }
        return ['success' => 1, 'data' => ['id' => $id]];
    }

    /**
     * 删除账单类型
     * @param Request $request
     */
    public function removeAccount(Request $request) {
        $id = $request->input('id');
        $wx_id = $request->input('wx_id');
        if(!is_null($id)) {
            WxAccount::where('id', $id)->where('wx_id', $wx_id)->delete();
            return ['success' => 1];
        }

        return ['success' => 0];
    }

    /**
     * 账单类型表单验证
     * @param $request
     * @return mixed
     */
    private function accountValidator($request)
    {
        $messages = [
            'account_type.required' => '请选择账单类型',
            'account_name.required' => '请填写账单名称',
            'account_name.max' => '账单名称最多64个字符',
            'account_name.min' => '账单名称最少2个字符',
            'wx_id.required' => '微信id不能为空',
            'single_price.required' => '请填写单价',
            'single_price.Numeric' => '单价必须是数值',
        ];

        $rules = [
            'account_type' => 'required',
            'wx_id' => 'required',
            'account_name' => 'required|max:64|min:2',
            'single_price' => 'required|Numeric'
        ];
        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * 添加或者更新账单记录
     * @param Request $request
     */
    public function addOrUpdateAccountLog(Request $request) {
        $validator = $this->accountLogValidator($request);
        if ($validator->fails()) {
            return ['success' => 0, 'msg' => $validator->errors()];
        }
        $form = [
            'amount' => $request->input('amount'),
            'wx_id' => $request->input('wx_id'),
            'account_name' => $request->input('account_name'),
            'single_price' => $request->input('single_price'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
        ];

        $id = $request->input('id');

        if (!is_null($id)) {
            WxAccountLog::where('id', $id)->update($form);
        } else {
            $info = new WxAccountLog($form);
            $info->save();
            $id = $info->id;
        }
        return ['success' => 1, 'data' => ['id' => $id]];
    }

    /**
     * 删除账单类型
     * @param Request $request
     */
    public function removeAccountLog(Request $request) {
        $id = $request->input('id');
        $wx_id = $request->input('wx_id');
        if(!is_null($id)) {
            WxAccountLog::where('id', $id)->where('wx_id', $wx_id)->delete();
            return ['success' => 1];
        }

        return ['success' => 0];
    }

    /**
     * 账单记录表单验证
     * @param $request
     * @return mixed
     */
    private function accountLogValidator($request)
    {
        $messages = [
            'account_name.required' => '请选择账单类型',
            'wx_id.required' => '微信id不能为空',
            'single_price.required' => '请填写单价',
            'single_price.Numeric' => '单价必须是数值',
            'amount.required' => '请填写单价',
            'amount.Numeric' => '单价必须是数值',
            'start_time.required' => '请选择开始时间',
            'start_time.date' => '开始时间格式不对',
            'end_time.required' => '请填写结束时间',
            'end_time.date' => '结束时间格式不正确',
        ];

        $rules = [
            'account_name' => 'required',
            'wx_id' => 'required',
            'amount' => 'required|Numeric',
            'single_price' => 'required|Numeric',
            'start_time' => 'required|date',
            'end_time' => 'required|date'
        ];
        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * 获取账户类型
     * @param Request $request
     * @return array
     */
    public function getAccountType(Request $request) {
        $wx_id = $request->input('wx_id');
        if(!is_null($wx_id)) {
            $types = WxAccountType::where('wx_id', $wx_id)
                ->orderBy('created_at', 'desc')
                ->with("accounts")
                ->get();

            return ['success'=>1, 'data'=>$types];
        }

        return ['success'=>0];
    }

    /**
     * 获取账户名称
     * @param Request $request
     * @return array
     */
    public function getAccount(Request $request) {
        $wx_id = $request->input('wx_id');
        $account_type = $request->input('account_type');
        if(!is_null($wx_id) and !is_null($account_type)) {
            $types = WxAccount::where('wx_id', $wx_id)
                ->where('account_type', $account_type)
                ->with("logs")
                ->orderBy('created_at', 'desc')
                ->get();

            return ['success'=>1, 'data'=>$types];
        }

        return ['success'=>0];
    }

    /**
     * 获取账户日志
     * @param Request $request
     * @return array
     */
    public function getAccountLog(Request $request) {
        $wx_id = $request->input('wx_id');
        $account_id = $request->input('account_id');

        if(!is_null($wx_id) and !is_null($account_id)) {
            $types = WxAccountLog::where('wx_id', $wx_id)
                ->where('account_id', $account_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return ['success'=>1, 'data'=>$types];
        }

        return ['success'=>0];
    }
}
