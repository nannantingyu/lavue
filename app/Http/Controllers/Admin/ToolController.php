<?php
/**
 * 工具管理
 * User: yangji
 * Date: 2018/7/30
 * Time: 上午11:11
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Tool;

class ToolController extends Controller
{

    protected function validator(array $data)
    {
        $messages = [
            'title.required' => '请输入名称',
            'description.required' => '描述',
            'image.required' => '请输入图片地址',
            'tag.required' => '显示标签',
            'sequence.required' => '请输入排序',
            'state.required' => '是否启用',
        ];

        $rules = [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required',
            'tag' => 'required',
            'sequence' => 'required',
            'state' => 'required',
        ];

        return Validator::make($data, $rules, $messages);
    }

    /** 添加工具
     *
     * @auth tool:update
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
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'image' => $request->input('image'),
            'tag' => $request->input('tag'),
            'sequence' => $request->input('sequence'),
            'state' => $request->input('state'),
            'url' => $request->input('url'),
        ];

        $id = $request->input('id');
        if (!is_null($id)) {
            Tool::where('id', $id)->update($form);
        } else {
            $tool = new Tool($form);
            $tool->save();
            $id = $tool->id;
        }
        return ['success' => 1, 'data' => ['id' => $id]];
    }

    /**
     * 获取所有的工具列表
     *
     * @auth tool:read
     * @param Request $request
     * @return array
     */
    public function getList(Request $request)
    {
        return ['success' => 1, 'data' => Tool::orderBy('id', 'DESC')->get()];
    }
}