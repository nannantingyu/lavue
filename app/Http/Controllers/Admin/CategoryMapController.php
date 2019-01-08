<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Models\CategoryMap;

class CategoryMapController extends Controller {
    /**
     * 获取分类列表
     * @return array
     */
    public function lists () {
//        $data = [];
//        return ['success'=>1, 'data'=>$data];
        return ['success'=>1, 'data'=>CategoryMap::orderBy('updated_at', 'desc')->get()];
    }

    /**
     * 获取具体的分类
     * @param $id int 根据id获取
     * @param $key string 根据key获取
     * @return array
     */
    public function info (Request $request) {
        $id = $request->input('id');

        if(!is_null($id) && \numcheck::is_int($id))
            return ['success'=>1, 'data'=>CategoryMap::find($id)];

        return ['success'=>0];
    }

    /**
     * 添加或者更新分类
     * @param $id int 根据id获取
     * @param $key string 根据key获取
     * @param $form array 表单数据
     */
    public function addCategoryMap(Request $request) {
        //验证表单
        $validate = $this->validateForm($request);
        if(!$validate['success']) {
            return response()->json(['success'=>0, "errors"=>$validate['msg']]);
        }

        //添加或者更新数据
        $form = [
            'source_category' => $request->input('source_category'),
            'source_site' => $request->input('source_site'),
            'target' => $request->input('target'),
            'state' => $request->input('state'),
        ];

        $id = $request->input('id');
        if(!is_null($id)) {
            CategoryMap::where('id', $id)->update($form);
        }
        else {
            $category = new CategoryMap($form);
            $category->save();
            $id = $category->id;
        }

        return ['success'=>1, 'data'=>['id'=>$id]];
    }

    /**
     * 表单验证
     * @param Request $request
     * @return array
     */
    private function validateForm(Request $request) {
        $messages = [
            'source_category.required' => '请输入原分类名称',
            'source_category.max' => '原分类名称长度不能超过20个字符',
            'source_category.min' => '原分类名称长度不能少于2个字符',
            'target.integer' => '转换的分类id不正确',
            'state.integer' => '状态不正确',
        ];

        $rules = [
            'source_category' => 'required|max:20|min:2',
            'target' => 'required|integer',
            'state' => 'required|integer',
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);
        if($validator->fails()) {
            return ['success'=>0, 'msg'=>$validator->errors()];
        }

        return ['success'=>1];
    }

    /**
     * 根据id设置分类状态
     * @param Request $request
     */
    public function setCategoryMapState(Request $request) {
        $id = $request->input('id');
        if(!is_null($id) and \numcheck::is_int($id)) {
            $state = $request->input('state');
            $state = $state === 1?$state:0;

            CategoryMap::where('id', $id)->update(['state'=>$state]);
            return ['success'=>1];
        }

        return ['success'=>0];
    }
}