<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Menu;

class MenuController extends Controller
{
    protected function validator(array $data)
    {
        $messages = [
            'name.required' => '请输入名称',
            'url.required' => '请输入url',
            'area.required' => '请输入显示区域',
            'sequence.required' => '请输入排序',
            'state.required' => '是否启用',
        ];

        $rules = [
            'name' => 'required',
            'url' => 'required',
            'area' => 'required',
            'sequence' => 'required',
            'state' => 'required',
        ];

        return Validator::make($data, $rules, $messages);
    }

    /** 添加菜单
     * @auth menu:update
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
            'name' => $request->input('name'),
            'url' => $request->input('url'),
            'target' => $request->input('target'),
            'area' => $request->input('area'),
            'sequence' => $request->input('sequence'),
            'state' => $request->input('state'),
            'title' => $request->input('title'),
            'keywords' => $request->input('keywords'),
            'description' => $request->input('description'),
        ];

        $id = $request->input('id');
        if (!is_null($id)) {
            Menu::where('id', $id)->update($form);
        } else {
            $menu = new Menu($form);
            $menu->save();
            $id = $menu->id;
        }
        return ['success' => 1, 'data' => ['id' => $id]];
    }

    /**
     * 获取所有的Menu配置
     * @auth menu:read
     * @param Request $request
     * @return array
     */
    public function getList(Request $request)
    {
        return ['success' => 1, 'data' => Menu::orderBy('sequence', 'DESC')->get()];
    }
}