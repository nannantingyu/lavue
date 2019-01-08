<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\CompanyCategory;

class CompanyController extends Controller {
    /**
     * 获取配置列表
     * @auth company:read
     * @return array
     */
    public function lists () {
        return ['success'=>1, 'data'=>Company::orderBy('updated_at', 'desc')->with('categories')->get()];
    }

    /**
     * 获取具体的配置
     * @auth company:read
     * @param $id int 根据id获取
     * @param $key string 根据key获取
     * @return array
     */
    public function info (Request $request) {
        $id = $request->input('id');

        if(!is_null($id) && \numcheck::is_int($id))
            return ['success'=>1, 'data'=>Company::find($id)];

        return ['success'=>0];
    }

    /**
     * 添加或者更新机构
     * @auth company:update
     * @param $id int 根据id获取
     * @param $key string 根据key获取
     * @param $form array 表单数据
     */
    public function addCompany(Request $request) {
        //验证表单
        $validate = $this->validateForm($request);
        if(!$validate['success']) {
            return response()->json(['success'=>0, "errors"=>$validate['msg']]);
        }

        //添加或者更新数据
        $form = [
            'name' => $request->input('name'),
            'shortname' => $request->input('shortname'),
            'link' => $request->input('link'),
            'tag' => $request->input('tag'),
            'logo' => $request->input('logo'),
            'state' => $request->input('state'),
            'sequence' => $request->input('sequence'),
        ];

        $id = $request->input('id');
        if(!is_null($id)) {
            Company::where('id', $id)->update($form);
        }
        else {
            $company = new Company($form);
            $company->save();
            $id = $company->id;
        }

        //更新分类
        $categories = $request->input('categories', []);
        if(is_array($categories) and count($categories) > 0) {
            CompanyCategory::where('cid', $id)->delete();
            $all_cats = [];
            foreach ($categories as $val) {
                array_push($all_cats, ['cid'=> $id, 'ccid'=>$val]);
            }

            CompanyCategory::insert($all_cats);
        }

        // 更新静态页
        $this->kafka->produce($this->static_topic, 'company');
        foreach ($categories as $ca) {
            $this->kafka->produce($this->static_topic, 'company/'.$ca);
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
            'name.required' => '请输入机构名称',
            'name.max' => '机构名称长度不能超过32个字符',
            'name.min' => '机构名称长度不能少于2个字符',
            'shortname.required' => '请输入缩略名称',
            'shortname.max' => '缩略名称长度不能超过16个字符',
            'shortname.min' => '缩略名称长度不能少于2个字符',
            'link.required' => '链接不能为空',
            'tag.required' => '标签不能为空',
            'logo.required' => 'Logo不能为空',
            'state.integer' => '状态不正确',
            'sequence.integer' => '排序不正确',
        ];

        $rules = [
            'name' => 'required|max:32|min:2',
            'shortname' => 'required|max:16|min:2',
            'link' => 'required|max:256|min:2',
            'tag' => 'required|max:64|min:2',
            'logo' => 'required|max:256|min:2',
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
     * 根据id设置company状态
     * @auth company:delete
     * @param Request $request
     */
    public function setCompanyState(Request $request) {
        $id = $request->input('id');
        if(!is_null($id) and \numcheck::is_int($id)) {
            $state = $request->input('state');
            $state = $state === 1?$state:0;

            Company::where('id', $id)->update(['state'=>$state]);
            return ['success'=>1];
        }

        return ['success'=>0];
    }
}