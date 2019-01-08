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
use App\Models\EconomicEvent;

class CalendarEventController extends Controller
{

    protected function validator(array $data)
    {
        $messages = [
            'country.required' => '请输入国家',
            'time.required' => '请输入发布时间',
            'city.required' => '请输入城市',
            'importance.required' => '请输入重要性',
            'event.required' => '请输入事件',
            'date.required' => '请输入事件日期'
        ];

        $rules = [
            'country' => 'required',
            'time' => 'required',
            'city' => 'required',
            'importance' => 'required',
            'event' => 'required',
            'date' => 'required'
        ];
        return Validator::make($data, $rules, $messages);
    }

    /** 添加财经事件
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
            'city' => $request->input('city'),
            'importance' => $request->input('importance'),
            'event' => $request->input('event'),
            'date' => $request->input('date'),
            'source_id' => $request->input('source_id')
        ];

        $id = $request->input('id');
        if (!is_null($id)) {
            EconomicEvent::where('id', $id)->update($form);
        } else {
            $info = new EconomicEvent($form);
            $info->save();
            $id = $info->id;
        }
        return ['success' => 1, 'data' => ['id' => $id]];
    }

    /**
     * 获取财经事件
     * @param Request $request
     * @return array
     */
    public function getList(Request $request)
    {
        $page = $request->input('page');
        $pageSize = $request->input('pageSize');
        $count = EconomicEvent::count();
        $value = EconomicEvent::orderBy('id', 'DESC')
            ->forPage($page, $pageSize)
            ->get();
        $data =[
            "list" => $value,
            'count' => $count,
            'page' => $page,
            'pageSize' => $pageSize
        ];
        return ['success' => 1, 'data' => $data];
    }
}