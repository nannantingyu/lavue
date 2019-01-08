<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\CrawlArticle;
use Illuminate\Support\Facades\Auth;
use App\Support\Kafka;

class CrawlArticleController extends Controller
{
    public function __construct(Kafka $kafka)
    {
        $this->kafka = $kafka;
    }

    /**
     * 获取要抓取的文章列表
     *
     * @auth crawl-article:read
     * @return array
     */
    public function lists()
    {
        return ['success' => 1, 'data' => CrawlArticle::orderBy('state', 'desc')->orderBy('created_at', 'desc')->get()];
    }

    /**
     * 获取具体的抓取信息
     *
     * @auth crawl-article:read
     * @param $id int 根据id获取
     * @return array
     */
    public function info(Request $request)
    {
        $id = $request->input('id');
        if (!is_null($id) && \numcheck::is_int($id))
            return ['success' => 1, 'data' => CrawlArticle::find($id)];

        return ['success' => 0];
    }

    /**
     * 添加或者更新抓取列表
     *
     * @auth crawl-article:update
     * @param $id int 根据id获取
     * @param $key string 根据key获取
     * @param $form array 表单数据
     */
    public function addCrawlArticle(Request $request)
    {
        //验证表单
        $validate = $this->validateForm($request);
        if (!$validate['success']) {
            return response()->json(['success' => 0, "errors" => $validate['msg']]);
        }

        //添加或者更新数据
        $url = $request->input('url');
        $categories = $request->input('categories');
        $categories = implode(',', $categories);

        $form = [
            'url' => $url,
            'categories' => $categories,
            'user_id' => Auth::user()->id,
        ];

        $id = $request->input('id');

        if (!is_null($id)) {
            CrawlArticle::where('id', $id)->update($form);
        } else {
            $crawlArticle= new CrawlArticle($form);
            $crawlArticle->save();
            $id = $crawlArticle->id;
        }

        // 更新静态页
        $this->addCrawlJob($url);
        return ['success' => 1, 'data' => ['id' => $id]];
    }

    /**
     * 表单验证
     * @param Request $request
     * @return array
     */
    private function validateForm(Request $request)
    {
        $messages = [
            'url.required' => '请输入链接',
            'state.integer' => '状态不正确',
        ];

        $rules = [
            'url' => 'required|max:512|min:2',
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return ['success' => 0, 'msg' => $validator->errors()];
        }

        return ['success' => 1];
    }

    private function addCrawlJob($url)
    {
        $this->kafka->produce('crawl_single_article', $url);
    }
}