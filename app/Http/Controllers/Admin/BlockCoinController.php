<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\BlockCoin;

class BlockCoinController extends Controller
{
    /**
     * 获取币种列表
     *
     * @auth block-coin:read
     * @return array
     */
    public function lists()
    {
        return ['success' => 1, 'data' => BlockCoin::orderBy('sequence', 'asc')->get()];
    }

    /**
     * 获取具体的币信息
     *
     * @auth block-coin:read
     * @param $id int 根据id获取
     * @return array
     */
    public function info(Request $request)
    {
        $id = $request->input('id');

        if (!is_null($id) && \numcheck::is_int($id))
            return ['success' => 1, 'data' => BlockCoin::find($id)];

        return ['success' => 0];
    }

    /**
     * 添加或者更新币种
     *
     * @auth block-coin:update
     * @param $id int 根据id获取
     * @param $form array 表单数据
     */
    public function addBlockCoin(Request $request)
    {
        //验证表单
        $validate = $this->validateForm($request);
        if (!$validate['success']) {
            return response()->json(['success' => 0, "errors" => $validate['msg']]);
        }

        $coin_id = $request->input('coin_id');
        $in_db = BlockCoin::where('coin_id', $coin_id)->first();
        if($in_db) {
            return ['success'=>0, 'data'=>'币已存在'];
        }

        //添加或者更新数据
        $form = [
            'coin_id' => $coin_id,
            'coin_name' => $request->input('coin_name'),
            'symble' => $request->input('symble'),
            'state' => $request->input('state'),
            'sequence' => $request->input('sequence'),
        ];

        $id = $request->input('id');
        if (!is_null($id)) {
            BlockCoin::where('id', $id)->update($form);
        } else {
            $blockCoin = new BlockCoin($form);
            $blockCoin->save();
            $id = $blockCoin->id;
        }

        // 更新静态页
        $this->updateBlockCoinTemplate();
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
            'coin_id.required' => '请输入币的代号',
            'coin_id.max' => '币的代号长度不能超过32个字符',
            'coin_id.min' => '币的代号长度不能少于2个字符',
            'coin_name.required' => '请输入币的名称',
            'coin_name.max' => '币的名称长度不能超过32个字符',
            'coin_name.min' => '币的名称长度不能少于2个字符',
            'symble.required' => '请输入币的缩写',
            'symble.max' => '币的缩写长度不能超过32个字符',
            'symble.min' => '币的缩写长度不能少于2个字符',
            'state.integer' => '状态不正确',
            'sequence.integer' => '排序不正确',
        ];

        $rules = [
            'coin_id' => 'required|max:64|min:2',
            'coin_name' => 'required|max:64|min:2',
            'symble' => 'required|max:64|min:2',
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
     * 根据id设置Coin状态
     *
     * @auth block-coin:delete
     */
    public function setBlockCoinState(Request $request)
    {
        $id = $request->input('id');
        if (!is_null($id) and \numcheck::is_int($id)) {
            $state = $request->input('state');
            $state = $state === 1 ? $state : 0;

            BlockCoin::where('id', $id)->update(['state' => $state]);
            // 更新静态页
//            $this->updateBlockCoinTemplate();
            return ['success' => 1];
        }

        return ['success' => 0];
    }

    /**
     * 更新前端模版
     */
    private function updateBlockCoinTemplate()
    {
        $this->template_updater->update_page('virtualcurrency');
    }
}