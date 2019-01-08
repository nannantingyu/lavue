<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Kuaixun;

class KuaiXunController extends Controller
{

    /**
     * 根据页码查询快讯列表
     * @param Request $request
     * @return array
     */
    public function getPageList(Request $request)
    {
        $type = $request->input('type');
        $pageSize = $request->input('pageSize');
        $page = $request->input('page');
        $order = $request->input('order');
        $isDesc = $request->input('isDesc');
        $state = $request->input('state');

        $kain = new Kuaixun();
        $value = $kain->getPageList($type, $page, $pageSize,$state, $order, $isDesc);
        return ['success' => 1, 'data' => $value];
    }

    /**
     * 根据时间查询快讯列表
     * @param Request $request
     * @return array
     */
    public function getList(Request $request)
    {
        $type = $request->input('type');
        $startTime = $request->input('startTime');
        $endTime = $request->input('endTime');
        $kain = new Kuaixun();
        $list = $kain->getList($type, $startTime, $endTime);
        return ['success' => 1, 'data' => $list];
    }


    /**
     */
    protected function validator(array $data)
    {
        $messages = [
            'title.required' => '请输入快讯标题',
            'body.required' => '请输入快讯内容',
            'importance.required' => '是否重要',
        ];

        $rules = [
            'title' => 'required',
            'body' => 'required',
            'importance' => ['required'],
        ];

        return Validator::make($data, $rules, $messages);
    }

    /**
     * 添加或者修改快讯
     * @param Request $request
     * @return array
     */
    public function addKuaiXun(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return ['success' => 0, 'msg' => $validator->errors()];
        }

        $importance = $request->input('importance');

        if ($importance != 1 && $importance != 0) {
            //如果重要性 不是0或者1
            return ['success' => 0, 'msg' => '重要性只能是0或者1'];
        }

        $form = [
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'keywords' => $request->input('keywords'),
            'body' => $request->input('body'),
            'source_id' => $request->input('source_id'),
            'publish_time' => $request->input('publish_time'),
            'importance' => $request->input('importance'),
            'more_link' => $request->input('more_link'),
            'image' => $request->input('image'),
            'type' => $request->input('type'),
            'former_value' => $request->input('former_value'),
            'predicted_value' => $request->input('predicted_value'),
            'published_value' => $request->input('published_value'),
            'country' => $request->input('country'),
            'influnce' => $request->input('influnce'),
            'star' => $request->input('star'),
            'status' => $request->input('state'),
        ];


        $id = $request->input('id');
        if (!is_null($id)) {
            Kuaixun::where('id', $id)->update($form);
        } else {
            $kuaiXun = new Kuaixun($form);
            $kuaiXun->save();
            $id = $kuaiXun->id;
        }

        return ['success' => 1, 'data' => ['id' => $id]];
    }


}