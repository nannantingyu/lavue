<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Models\CategoryCompany;

class CategoryCompanyController extends Controller {
    /**
     * 获取分类列表
     * @return array
     */
    public function lists () {
        return ['success'=>1, 'data'=>CategoryCompany::orderBy('updated_at', 'desc')->get()];
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
            return ['success'=>1, 'data'=>CategoryCompany::find($id)];

        return ['success'=>0];
    }

    /**
     * 添加或者更新分类
     * @param $id int 根据id获取
     * @param $key string 根据key获取
     * @param $form array 表单数据
     */
    public function addCategoryCompany(Request $request) {
        //验证表单
        $validate = $this->validateForm($request);
        if(!$validate['success']) {
            return response()->json(['success'=>0, "errors"=>$validate['msg']]);
        }

        //添加或者更新数据
        $form = [
            'name' => $request->input('name'),
            'state' => $request->input('state'),
            'sequence' => $request->input('sequence'),
        ];

        $id = $request->input('id');
        if(!is_null($id)) {
            CategoryCompany::where('id', $id)->update($form);
        }
        else {
            $category = new CategoryCompany($form);
            $category->save();
            $id = $category->id;
        }

        // 更新静态页
        $this->updateCategoryCompanyTemplate($id);
        return ['success'=>1, 'data'=>['id'=>$id]];
    }

    /**
     * 表单验证
     * @param Request $request
     * @return array
     */
    private function validateForm(Request $request) {
        $messages = [
            'name.required' => '请输入分类名称',
            'name.max' => '分类名称长度不能超过20个字符',
            'name.min' => '分类名称长度不能少于2个字符',
            'state.integer' => '状态不正确',
            'sequence.integer' => '排序不正确',
        ];

        $rules = [
            'name' => 'required|max:20|min:2',
            'sequence' => 'required|integer',
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
    public function setCategoryCompanyState(Request $request) {
        $id = $request->input('id');
        if(!is_null($id) and \numcheck::is_int($id)) {
            $state = $request->input('state');
            $state = $state === 1?$state:0;

            CategoryCompany::where('id', $id)->update(['state'=>$state]);

            // 更新静态页
            return ['success'=>1];
        }

        $this->updateCategoryCompanyTemplate($id);
        return ['success'=>0];
    }

    /**
     * 更新静态页
     * @param $id
     * @param string $type
     */
    public function updateCategoryCompanyTemplate($id, $type='update') {
        $this->template_updater->update('company/'.$id, $type);
        $this->template_updater->update_page('company');
    }
}