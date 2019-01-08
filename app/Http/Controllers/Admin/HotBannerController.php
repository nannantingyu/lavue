<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\HotBanner;

class HotBannerController extends Controller
{
    /**
     * 获取热门广告列表
     *
     * @auth hot-banner:read
     * @return array
     */
    public function lists()
    {
        return ['success' => 1, 'data' => HotBanner::orderBy('sequence', 'asc')->get()];
    }

    /**
     * 获取具体的热门广告
     *
     * @auth hot-banner:read
     * @param $id int 根据id获取
     * @param $key string 根据key获取
     * @return array
     */
    public function info(Request $request)
    {
        $id = $request->input('id');
        $key = $request->input('key');

        if (!is_null($id) && \numcheck::is_int($id))
            return ['success' => 1, 'data' => HotBanner::find($id)];

        return ['success' => 0];
    }

    /**
     * 添加或者更新热门广告
     *
     * @auth hot-banner:update
     * @param $id int 根据id获取
     * @param $key string 根据key获取
     * @param $form array 表单数据
     */
    public function addHotBanner(Request $request)
    {
        //验证表单
        $validate = $this->validateForm($request);
        if (!$validate['success']) {
            return response()->json(['success' => 0, "errors" => $validate['msg']]);
        }

        //添加或者更新数据
        $form = [
            'image' => $request->input('image'),
            'title' => $request->input('title'),
            'link' => $request->input('link'),
            'state' => $request->input('state'),
            'sequence' => $request->input('sequence'),
        ];

        $id = $request->input('id');
        if (!is_null($id)) {
            HotBanner::where('id', $id)->update($form);
        } else {
            $hotbanner = new HotBanner($form);
            $hotbanner->save();
            $id = $hotbanner->id;
        }

        // 更新静态页
        $this->updateHotBannerTemplate();
        return ['success' => 1, 'data' => ['id' => $id]];
    }

    /**
     * 表单验证
     *
     * @param Request $request
     * @return array
     */
    private function validateForm(Request $request)
    {
        $messages = [
            'title.required' => '请输入标题',
            'title.max' => '标题长度不能超过32个字符',
            'title.min' => '标题长度不能少于2个字符',
            'image.required' => '图片不能为空',
            'link.required' => '链接不能为空',
            'link.max' => '分组长度最多256个字符',
            'state.integer' => '状态不正确',
            'sequence.integer' => '排序不正确',
        ];

        $rules = [
            'title' => 'required|max:64|min:2',
            'image' => 'required',
            'link' => 'required|min:2|max:256',
            'sequence' => 'required|integer',
            'state' => 'required|integer',
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return ['success' => 0, 'msg' => $validator->errors()];
        }

        return ['success' => 1];
    }

    /**
     * 根据id设置hotbanner状态
     *
     * @auth hot-banner:delete
     */
    public function setHotBannerState(Request $request)
    {
        $id = $request->input('id');
        if (!is_null($id) and \numcheck::is_int($id)) {
            $state = $request->input('state');
            $state = $state === 1 ? $state : 0;

            HotBanner::where('id', $id)->update(['state' => $state]);
            // 更新静态页
            $this->updateHotBannerTemplate();
            return ['success' => 1];
        }

        return ['success' => 0];
    }

    private function updateHotBannerTemplate()
    {
        $this->template_updater->update_page('index');
    }
}