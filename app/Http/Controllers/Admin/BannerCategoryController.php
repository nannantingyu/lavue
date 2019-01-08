<?php
/**
 * 活动分类
 * User: yangji
 * Date: 2018/8/1
 * Time: 下午3:42
 */

namespace App\Http\Controllers\Admin;

use App\Models\BannerCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BannerCategoryController extends Controller
{

    private function validator($request)
    {
        $messages = [
            'name.required' => '请输入标题',
            'name.max' => '标题长度不能超过64个字符',
            'name.min' => '标题长度不能少于2个字符',
            'pid.required' => '父idD不能为空',
            'pid.integer' => '父idD不正确',
            'sequence.required' => '排序不能为空',
            'sequence.integer' => '排序不正确',
            'state.required' => '状态不能为空',
            'state.boolean' => '排序不正确',
        ];

        $rules = [
            'name' => 'required|max:64|min:2',
            'pid' => 'required|integer',
            'sequence' => 'required|integer',
            'state' => 'required|boolean',
        ];
        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * 添加活动
     */
    public function add(Request $request)
    {
        $validator = $this->validator($request);
        if ($validator->fails()) {
            return ['success' => 0, 'msg' => $validator->errors()];
        }
        $form = [
            'name' => $request->input('name'),
            'pid' => $request->input('pid'),
            'sequence' => $request->input('sequence'),
            'state' => $request->input('state'),
        ];

        $id = $request->input('id');

        if (!is_null($id)) {
            BannerCategory::where('id', $id)->update($form);
        } else {
            $info = new BannerCategory($form);
            $info->save();
            $id = $info->id;
        }
        return ['success' => 1, 'data' => ['id' => $id]];
    }

    /**
     * 获取所有活动
     */
    public function getList(Request $request)
    {
        return ['success' => 1, 'data' => BannerCategory::orderBy('id', 'desc')->get()];
    }

    /**
     * 更改banner活动分类状态
     * @param Request $request
     * @return array
     */
    public function setState(Request $request)
    {
        $id = $request->input('id');
        if (!is_null($id) and \numcheck::is_int($id)) {
            $state = $request->input('state');
            $state = $state === 1 ? $state : 0;
            BannerCategory::where('id', $id)->update(['state' => $state]);
            return ['success' => 1];
        }
        return ['success' => 0];
    }
}