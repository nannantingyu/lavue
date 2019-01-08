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
use App\Models\EconomicCalendar;

    class CalendarController extends Controller
{

    protected function validator(array $data)
    {
        $messages = [
            'country.required' => '请输入国家',
            'quota_name.required' => '请输入指标名称',
            'publish_time.required' => '请输入发布时间',
            'importance.required' => '请输入重要性',
            'influence.required' => '请输入影响',
            'dataname.required' => '请输入指标名称',
            'datename.required' => '请输入指标时间',
            'dataname_id.required' => '请输入指标id, 关联jiedu表',
        ];

        $rules = [
            'country' => 'required',
            'quota_name' => 'required',
            'publish_time' => 'required',
            'importance' => 'required',
            'influence' => 'required',
            'source_id' => 'required',
            'dataname' => 'required',
            'datename' => 'required',
            'dataname_id' => 'required',
        ];

        return Validator::make($data, $rules, $messages);
    }

    /** 添加财经日历
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
            'quota_name' => $request->input('quota_name'),
            'publish_time' => $request->input('publish_time'),
            'importance' => $request->input('importance'),
            'former_value' => $request->input('former_value'),
            'predicted_value' => $request->input('predicted_value'),
            'published_value' => $request->input('published_value'),
            'influence' => $request->input('influence'),
            'source_id' => $request->input('source_id'),
            'dataname' => $request->input('dataname'),
            'datename' => $request->input('datename'),
            'dataname_id' => $request->input('dataname_id'),
            'unit' => $request->input('unit')
        ];

        $id = $request->input('id');
        if (!is_null($id)) {
            EconomicCalendar::where('id', $id)->update($form);
        } else {
            $info = new EconomicCalendar($form);
            $info->save();
            $id = $info->id;
        }
        return ['success' => 1, 'data' => ['id' => $id]];
    }

    /**
     * 获取财经日历
     * @param Request $request
     */
    /**
     * 获取用户反馈
     * @param Request $request
     * @return array
     */
    public function getList(Request $request)
    {
        $page = $request->input('page');
        $pageSize = $request->input('pageSize');

        $info = new EconomicCalendar();
        $value = $info->lists($page, $pageSize);
        return ['success' => 1, 'data' => $value];
    }


}