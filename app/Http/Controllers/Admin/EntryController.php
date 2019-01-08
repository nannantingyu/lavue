<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Models\Entry;
use App\Models\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EntryController extends Controller {
    /**
     * 获取分类列表
     * @return array
     */
    public function lists () {

        $data = Entry::orderBy('sequence', 'desc')->get();

        return ['success'=>1, 'data'=>$data];
    }

    /**
     * 获取具体的词条
     * @auth entry:read
     * @param $id int 根据id获取
     * @param $key string 根据key获取
     * @return array
     */
    public function info (Request $request) {
        $id = $request->input('id');

        if(!is_null($id) && \numcheck::is_int($id))
            return ['success'=>1, 'data'=>Entry::with("articles")->find($id)];

        return ['success'=>0];
    }

    /**
     * 添加或者更新分类
     *
     * @auth entry:update
     * @param $id int 根据id获取
     * @param $key string 根据key获取
     * @param $form array 表单数据
     */
    public function addEntry(Request $request) {
        //验证表单
        $validate = $this->validateForm($request);
        if(!$validate['success']) {
            return response()->json(['success'=>0, "errors"=>$validate['msg']]);
        }

        //添加或者更新数据
        $form = [
            'entry_name' => $request->input('entry_name'),
            'description' => $request->input('description'),
            'state' => $request->input('state'),
            'sequence' => $request->input('sequence'),
        ];

        $id = $request->input('id');
        if(!is_null($id)) {
            Entry::where('id', $id)->update($form);
        }
        else {
            $entry = new Entry($form);
            $entry->save();
            $id = $entry->id;
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
            'entry_name.required' => '请输入词条名称',
            'entry_name.max' => '分类名称长度不能超过20个字符',
            'entry_name.min' => '分类名称长度不能少于2个字符',
            'description.required' => '请输入词条描述',
            'description.max' => '词条描述长度不能超过512个字符',
            'description.min' => '词条描述长度不能少于2个字符',
            'state.integer' => '状态不正确',
            'sequence.integer' => '排序不正确',
        ];

        $rules = [
            'entry_name' => 'required|max:20|min:2',
            'description' => 'required|max:255|min:2',
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
     * @auth category:delete
     * @param Request $request
     */
    public function setEntryState(Request $request) {
        $id = $request->input('id');
        if(!is_null($id) and \numcheck::is_int($id)) {
            $state = $request->input('state');
            $state = 1 - $state;

            Entry::where('id', $id)->update(['state'=>$state]);
            return ['success'=>1, 'state'=>$state];
        }

        return ['success'=>0];
    }

    /**
     * 添加词条文章
     * @param Request $request
     */
    public function addEntryArticle(Request $request) {
        $entry_id = $request->input('entry_id');
        $article_id = $request->input('article_id');

        if (!is_null($entry_id) and !is_null($article_id)) {
            $exist = DB::table("entry_article")->where("entry_id", $entry_id)
                ->where("article_id", $article_id)
                ->first();

            if(is_null($exist)) {
                DB::table("entry_article")->insert([
                    "entry_id"=> $entry_id,
                    "article_id"=> $article_id
                ]);

                return ["success"=>1];
            }
            else {
                return ["success"=>-1, "msg"=>"已存在"];
            }
        }

        return ["success"=>0, "msg"=>"参数错误"];
    }

    public function removeEntryArticle(Request $request) {
        $entry_id = $request->input('entry_id');
        $article_id = $request->input('article_id');
        if (!is_null($entry_id) and !is_null($article_id)) {
            DB::table("entry_article")->where("entry_id", $entry_id)
                ->where("article_id", $article_id)
                ->delete();

            return ["success"=>1];
        }

        return ["success"=>0, "msg"=>"参数错误"];
    }
}