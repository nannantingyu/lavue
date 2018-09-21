<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\WxAccount;
use App\Models\WxAccountLog;
use App\Models\WxAccountType;
use Illuminate\Support\Facades\DB;

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
            'icon' => $request->input('icon'),
            'type' => $request->input('type') == '收入'?1:0,
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
     * 获取收藏的账单
     * @param Request $request
     */
    public function getFavorAccount(Request $request) {
        $wx_id = $request->input('wx_id');
        if(!is_null($wx_id)) {
            $account = WxAccount::where('favor', 1)->get();
            return ['success'=>1, 'data'=>$account];
        }

        return ['success'=>0];
    }

    /**
     * 收藏账单
     * @param Request $request
     */
    public function favorAccount(Request $request) {
        $id = $request->input('id');
        $wx_id = $request->input('wx_id');
        $favor = $request->input('favor');
        if(!is_null($id)) {
            WxAccount::where('id', $id)->where('wx_id', $wx_id)->update(['favor'=>$favor]);
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

        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');

        $st = strtotime($start_time);
        $et = strtotime($end_time);
        $t = $st;

        while ($t <= $et) {
            $info = new WxAccountLog([
                'amount' => $request->input('amount'),
                'wx_id' => $request->input('wx_id'),
                'account_name' => $request->input('account_name'),
                'single_price' => $request->input('single_price'),
                'start_time' => date("Y-m-d H:i:s", $t),
                'end_time' => date("Y-m-d 23:59:59", $t),
                'type' => $request->input('type') == '收入'?1:0,
                'account_description' => $request->input('account_description')
            ]);

            $info->save();
            $t += 3600*24;
        }

        return ['success' => 1];
    }

    /**
     * 添加或者更新账单记录
     * @param Request $request
     */
    public function updateAccountLog(Request $request) {
        $validator = $this->accountLogValidator($request);
        if ($validator->fails()) {
            return ['success' => 0, 'msg' => $validator->errors()];
        }

        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');


        $info = [
            'amount' => $request->input('amount'),
            'wx_id' => $request->input('wx_id'),
            'account_name' => $request->input('account_name'),
            'single_price' => $request->input('single_price'),
            'start_time' => $start_time,
            'end_time' => $end_time,
            'type' => $request->input('type') == '收入'?1:0,
            'account_description' => $request->input('account_description')
        ];

        WxAccountLog::where('id', $request->input('id'))->update($info);

        return ['success' => 1];
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
        if(!is_null($wx_id)) {
            $types = WxAccount::where('wx_id', $wx_id);
            if(!is_null($account_type)) {
                $types = $types->where('account_type', $account_type);
            }

            $types = $types->with("logs")
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
                ->where('account_name', $account_id)
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
    public function getAccountLogInfo(Request $request) {
        $wx_id = $request->input('wx_id');
        $account_id = $request->input('account_id');

        if(!is_null($wx_id) and !is_null($account_id)) {
            $types = WxAccountLog::where('wx_account_log.wx_id', $wx_id)
                ->where('wx_account_log.id', $account_id)
                ->join('wx_account', 'wx_account_log.account_name', 'wx_account.id')
                ->orderBy('wx_account_log.created_at', 'desc')
                ->select('wx_account_log.*', 'wx_account.account_name as name', 'wx_account.icon')
                ->first();

            return ['success'=>1, 'data'=>$types];
        }

        return ['success'=>0];
    }

    /**
     * 获取每个月的总支出和总收入
     * @param Request $request
     */
    public function getMonthAll(Request $request) {
        $wx_id = $request->input('wx_id');
        $year = $request->input('year');
        $month = $request->input('month');

        $startday = date("Y-m-d 00:00:00", strtotime("$year-$month-01"));
        $endday = date('Y-m-d 23:59:59', strtotime("$startday +1 month -1 day"));

        $account_name = $request->input('account_name');

        $all_data = WxAccountLog::where('wx_id', $wx_id)
            ->where('start_time', '>=', $startday)
            ->where('start_time', '<=', $endday);

        if(!is_null($account_name)) {
            $all_data = $all_data->where('account_name', $account_name);
        }

        $all_data = $all_data->get();

        $all_out = 0;
        $all_in = 0;
        $all_days = [];
        foreach ($all_data as $key=>$val) {
            if($val->type === 1) {
                $all_in += $val->single_price * $val->amount;
            }
            else {
                $all_out += $val->single_price * $val->amount;
            }

            if(!isset($all_days[date('Y-m-d', strtotime($val->start_time))])) {
                $all_days[date('Y-m-d', strtotime($val->start_time))] = [];
            }

            array_push($all_days[date('Y-m-d', strtotime($val->start_time))], $val);
        }

        return ['success'=>1, 'data'=>['all_in'=>$all_in, 'all_out'=>$all_out, 'days'=>$all_days]];
    }

    /**
     * 获取统计数据
     * @param Request $request
     */
    public function accountData(Request $request) {
        $type = $request->input('type');
        $wx_id = $request->input('wx_id');
        $start_date = $request->input('st', date("Y-m-01", strtotime("-3 month")));
        $end_date = $request->input('et', date("Y-m-01", strtotime("+1 month")-3600));
        $keyid = $request->input('keyid');

        $start_date = date("Y-m-d 00:00:00", strtotime($start_date));
        $end_date = date("Y-m-d 23:59:59", strtotime($end_date));
        if(!is_null($type) && !is_null($wx_id)) {
            if($type === 'account') {
                $data = WxAccountLog::where('account_name', $keyid)
                    ->where('wx_id', $wx_id)
                    ->where('start_time', '>=', $start_date)
                    ->where('end_time', '<=', $end_date)
                    ->orderBy('start_time', 'asc')
                    ->get();
            }
            elseif($type === 'account_type') {
                $accounts = WxAccount::where('account_type', $keyid)->select('id')->get();
                $data = WxAccountLog::whereIn('account_name', $accounts)
                    ->where('wx_id', $wx_id)
                    ->where('start_time', '>=', $start_date)
                    ->where('end_time', '<=', $end_date)
                    ->orderBy('start_time', 'asc')
                    ->get();
            }
            elseif($type === 'type') {
                $data = WxAccountLog::where('wx_id', $wx_id)
                    ->where('start_time', '>=', $start_date)
                    ->where('end_time', '<=', $end_date)
                    ->orderBy('start_time', 'asc')
                    ->get();
            }

            return ['success'=>1, 'data'=>$data];
        }

        return ['success'=>0];
    }

    /**
     * 获取今年的收入支出
     * @param Request $request
     */
    public function getThisYear(Request $request) {
        $wx_id = $request->input('wx_id');
        $first_day = date("Y-01-01 00:00:00");
        $end_day = date("Y-m-d H:i:s");

        if(!is_null($wx_id)) {
            $data = WxAccountLog::where('start_time', ">=", $first_day)
                ->where('end_time', "<=", $end_day)
                ->groupBy("type")
                ->select(DB::raw("type, sum(amount*single_price) as s"))
                ->get();

            return ['success'=>1, "data"=>$data];
        }

        return ['success'=>0];
    }
}