<?php
/**
 * 财经日历
 * User: yangji
 * Date: 2018/7/30
 * Time: 上午11:11
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\EconomicHoliday;

/**
 * 财经假日
 * @package App\Http\Controllers
 */
class CalendarHolidayController extends Controller
{

    protected function validator(array $data)
    {
        $messages = [
            'country.required' => '请输入国家',
            'time.required' => '请输入发布时间',
            'market.required' => '请输入市场',
            'holiday_name.required' => '请输入假期名称',
            'detail.required' => '请输入假期详情',
            'date.required' => '请输入假期时间'
        ];

        $rules = [
            'country' => 'required',
            'time' => 'required',
            'market' => 'required',
            'holiday_name' => 'required',
            'detail' => 'required',
            'date' => 'required'
        ];
        return Validator::make($data, $rules, $messages);
    }

    /** 添加财经假期
     * @param Request $request
     * @return array
     */
    public function add(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return ['success' => 0, 'msg' => $validator->errors()];
        }

        $form = [
            'country' => $request->input('country'),
            'time' => $request->input('time'),
            'market' => $request->input('market'),
            'holiday_name' => $request->input('holiday_name'),
            'detail' => $request->input('detail'),
            'date' => $request->input('date'),
            'source_id' => $request->input('source_id')
        ];

        $id = $request->input('id');
        if (!is_null($id)) {
            EconomicHoliday::where('id', $id)->update($form);
        } else {
            $info = new EconomicHoliday($form);
            $info->save();
            $id = $info->id;
        }
        return ['success' => 1, 'data' => ['id' => $id]];
    }

    /**
     * 获取财经假期
     * @param Request $request
     * @return array
     */
    public function getList(Request $request)
    {
        $page = $request->input('page');
        $pageSize = $request->input('pageSize');
        $count = EconomicHoliday::count();
        $value = EconomicHoliday::orderBy('id', 'DESC')
            ->forPage($page, $pageSize)
            ->get();
        $data =[
            "list" => $value,
            'count' => $count,
            'page' => $page,
            'pageSize' => $pageSize
        ];
        return ['success' => 1, 'data' => $data];

//        return [
//            "list" => $value,
//            'count' => $count,
//            'page' => $page,
//            'pageSize' => $pageSize
//        ];
    }
}