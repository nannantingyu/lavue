<?php
/**
 * Created by PhpStorm.
 * User: yangji
 * Date: 2018/8/1
 * Time: 下午2:18
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Banner;

class BannerController extends Controller
{

    /**
     * 验证数据
     */
    private function validator(Request $request)
    {
        $messages = [
            'title.required' => '请输入标题',
            'title.max' => '标题长度不能超过32个字符',
            'title.min' => '标题长度不能少于2个字符',
            'image.required' => '图片不能为空',
            'page.required' => 'page不能为空',
            'link.required' => '链接不能为空',
            'link.max' => '分组长度最多256个字符',
            'cid.required' => '活动分类不能为空',
            'cid.integer' => '活动分类不正确',
            'hits.required' => '点击次数不能为空',
            'hits.integer' => '点击次数不正确',
            'state.boolean' => '状态不正确',
            'expire_time.required' => '过期日期不能为空',
            'expire_time.date' => '过期日期不正确',
            'sequence.required' => '排序不能为空',
            'sequence.integer' => '排序不正确',
        ];

        $rules = [
            'title' => 'required|max:64|min:2',
            'image' => 'required',
            'page' => 'required',
            'link' => 'required|min:2|max:256',
            'cid' => 'required|integer',
            'hits' => 'required|integer',
            'state' => 'required|boolean',
            'expire_time' => 'required|date',
            'sequence' => 'required|integer',
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * 添加活动
     *
     * @auth banner:update
     */
    public function add(Request $request)
    {
        $validator = $this->validator($request);
        if ($validator->fails()) {
            return ['success' => 0, 'msg' => $validator->errors()];
        }
        $form = [
            'title' => $request->input('title'),
            'page' => $request->input('page'),
            'image' => $request->input('image'),
            'link' => $request->input('link'),
            'cid' => $request->input('cid'),
            'hits' => $request->input('hits'),
            'state' => $request->input('state'),
            'sequence' => $request->input('sequence'),
            'expire_time' => $request->input('expire_time'),
        ];

        $id = $request->input('id');

        if (!is_null($id)) {
            Banner::where('id', $id)->update($form);
        } else {
            $info = new Banner($form);
            $info->save();
            $id = $info->id;
        }
        return ['success' => 1, 'data' => ['id' => $id]];
    }

    /**
     * 获取所有活动
     *
     * @auth banner:read
     */
    public function getList(Request $request)
    {
        return ['success' => 1, 'data' => Banner::orderBy('id', 'desc')->get()];
    }

    /**
     * 更改banner状态
     *
     * @auth banner:delete
     * @param Request $request
     * @return array
     */
    public function setState(Request $request)
    {
        $id = $request->input('id');
        if (!is_null($id) and \numcheck::is_int($id)) {
            $state = $request->input('state');
            $state = $state === 1 ? $state : 0;
            Banner::where('id', $id)->update(['state' => $state]);
            return ['success' => 1];
        }
        return ['success' => 0];
    }

}


