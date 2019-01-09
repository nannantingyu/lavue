<?php

namespace App\Http\Controllers\Admin;

use App\Models\Config;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\ArticleBody;
use App\Models\ArticleCategory;
use Illuminate\Support\Facades\DB;
use App\Support\Kafka;
use App\Support\TemplateUpdater;

class ArticleController extends Controller
{
    public function __construct(Kafka $kafka, TemplateUpdater $template_updater)
    {
        parent::__construct($template_updater);
        $this->kafka = $kafka;
    }

    /**
     * 获取文章列表
     * @auth article:read
     * @return array
     */
    public function lists(Request $request)
    {
        $num = $request->input('num', 10);
        $order = $request->input('order', 'asc');
        $order_by = $request->input('order_by', 'publish_time');
        $sites = $request->input('sites');
        $state = $request->input('state', 3);
        $category = $request->input('category');
        $keywords = $request->input('keywords');
        $st = $request->input('st');
        $et = $request->input('et');
        $time_key = $request->input('time_key', 'publish_time');

        $articles = DB::table('weixin_article')->orderBy($order_by, $order);
        if(!is_null($sites)) {
            $sites = explode(',', $sites);
            $articles = $articles->whereIn('source_site', $sites);
        }

        if(!is_null($st)) {
            $articles = $articles->where($time_key, ">=", $st);
        }

        if(!is_null($et)) {
            $articles = $articles->where($time_key, "<=", $et);
        }

        if($state != 3) {
            $articles = $articles->where('state', $state);
        }

        if(!is_null($category)) {
            $articles = $articles->join('article_category', 'article.id', '=', 'article_category.aid')
                ->whereIn('article_category.cid', explode(',', $category));
        }

        if(!is_null($keywords)) {
            if (\numcheck::is_int($keywords)) {
                $articles = $articles->where('id', '=', $keywords);
            }
            else {
                $articles = $articles->where(function ($query) use ($keywords) {
                    $query->where('title', 'like', '%'.$keywords.'%')
                        ->OrWhere('description', 'like', '%'.$keywords.'%');
                });
            }
        }

        $articles = $articles->select('weixin_article.*')->paginate($num);

        return ['success' => 1, 'data' => $articles];
    }

    /**
     * @auth article:read
     * 获取所有的source_site
     * @return array
     */
    public function source_site() {
        return ['success'=>1, 'data'=>Article::pluck('source_site')];
    }

    /**
     * @auth article:read
     * 获取文章所有来源
     * */
    public function source()
    {
        $source = DB::table('article')
            ->select('source_site')
            ->groupBy('source_site')
            ->get();
        return ['success' => 1, 'data' => $source];

    }

    /**
     * @auth article:read
     * 根据分类获取文章列表
     */
    public function articleListsByCategory(Request $request)
    {
        $categories = $request->input('categories');
        if (!is_null($categories)) {
            $articles = DB::table('article_category')
                ->join('article', 'article_category.aid', '=', 'article.id')
                ->whereIn('article_category.cid', explode(',', $categories))
                ->select('article.*')
                ->orderBy('article.publish_time', 'desc')
                ->get();

            return ['success' => 1, 'data' => $articles];
        }

        return ['success' => 0];
    }

    /**
     * @auth article:read
     * 获取单个文章详情
     */
    public function info(Request $request)
    {
        $id = $request->input('id');
        if (!is_null($id)) {
            $article = Article::with('body')->with('categories')->find($id);
            return ['success' => 1, 'data' => $article];
        }

        return ['success' => 0];
    }

    /**
     * @auth article:delete
     * @param id 文章id
     * 删除文章
     */
    public function deleteArticle(Request $request)
    {
        $id = $request->input('id');
        if (!is_null($id) and \numcheck::is_int($id)) {
            Article::where('id', $id)->delete();

            $this->updateArticleTemplate($id, 'delete');
            return ['success' => 1];
        }

        return ['success' => 0];
    }

    /**
     * 搜索文章
     * @auth article:read
     * @param keyword 关键词
     */
    public function searchArticle(Request $request)
    {
        $keywords = $request->input('keywords');
        if (!is_null($keywords)) {

            $articles = Article::where('state', 1);
            if (\numcheck::is_int($keywords)) {
                $articles = $articles->where('id', 'like', $keywords);
            } else {
                $articles = $articles->where('title', 'like', '%' . $keywords . '%');
            }

            $articles = $articles->select('id', 'title', 'image', 'publish_time', 'created_time', 'updated_time', 'state')
                ->orderBy('publish_time', 'desc')
                ->get();

            return ['success' => 1, 'data' => $articles];
        }
    }

    /**
     * 设置文章发布状态
     * @auth article:delete
     * @param Request $request
     */
    public function setArticleState(Request $request)
    {
        $id = $request->input('id');
        if (!is_null($id) and \numcheck::is_int($id)) {
            $state = $request->input('state');
            $state = $state == 1 ? 1 : 0;

            $this->updateArticleTemplate($id, $state == 1 ? 'update' : 'delete');
            Article::where('id', $id)->update([
                'state' => $state
            ]);

            return ['success' => 1];
        }
        return ['success' => 0];
    }


    /**
     * 批量设置文章状态
     * @auth article:delete
     * @param Request $request
     * @return array
     */
    public function setState(Request $request)
    {
        $id = $request->input('id');
        DB::table('article')
            ->whereIn('id', explode(",",$id))
            ->update(['state' => 1]);
        return [
            'success' => 1
        ];
    }

    /**
     * 批量设置文章状态
     * @auth article:delete
     * @param $request
     * @param $state
     */
    private function multiSetState($request, $state) {
        $is_selected = $request->input('is_selected');
        $selected = $request->input('selected');
        if($is_selected == 1) {
            if(is_array($selected) && count($selected) > 0) {
                Article::whereIn('id', $selected)->update(['state'=> $state]);
            }
        }
        elseif($is_selected == 0) {
            $articles = $this->getFilterParams($request);
            $articles->update([
                'state' => $state
            ]);
        }
    }

    /**
     * 批量删除文章
     * @auth article:delete
     * @param $request
     * @param $state
     */
    public function multiDelete($request, $state) {
        $is_select = $request->input('is_select');
        $selected = $request->input('selected');
        if($is_select === 'true') {
            if(is_array($selected) && count($selected) > 0) {
                Article::whereIn('id', $selected)->delete();
            }
        }
        else {
            $articles = $this->getFilterParams($request);
            $articles->delete();
        }

        return ['success'=>1];
    }

    /**
     * 批量下线文章
     * @auth article:delete
     * @param Request $request
     */
    public function multiOffline(Request $request) {
        $this->multiSetState($request, 0);
        return ['success'=>1];
    }

    /**
     * 批量上线文章
     * @auth article:delete
     * @param Request $request
     */
    public function multiOnline(Request $request) {
        $this->multiSetState($request, 1);
        return ['success'=>1];
    }

    /**
     * 获取筛选的文章
     * @auth article:read
     * @param $request
     * @return \Illuminate\Database\Query\Builder
     */
    private function getFilterParams($request) {
        $sites = $request->input('sites');
        $category = $request->input('category');
        $keywords = $request->input('keywords');

        $articles = DB::table('article');
        if(!is_null($sites)) {
            $sites = explode(',', $sites);
            $articles = $articles->whereIn('source_site', $sites);
        }

        if(!is_null($category)) {
            $articles = $articles->join('article_category', 'article.id', '=', 'article_category.aid')
                ->whereIn('article_category.cid', explode(',', $category));
        }

        if(!is_null($keywords)) {
            if (\numcheck::is_int($keywords)) {
                $articles = $articles->where('id', '=', $keywords);
            }
            else {
                $articles = $articles->where(function ($query) use ($keywords) {
                    $query->where('title', 'like', '%'.$keywords.'%')
                        ->OrWhere('description', 'like', '%'.$keywords.'%');
                });
            }
        }

        return $articles;
    }

    /**
     * 添加或者更新文章
     * @auth article:update
     * @param Request $request
     */
    public function addArticle(Request $request)
    {
        //验证表单
        $validate = $this->validateForm($request);
        if (!$validate['success']) {
            return response()->json(['success' => 0, "errors" => $validate['msg']]);
        }

        $form = [
            'title' => $request->input('title'),
            'image' => json_encode([$request->input('image')]),
            'description' => $request->input('description'),
            'keywords' => $request->input('keywords'),
            'author' => $request->input('author'),
            'publish_time' => $request->input('publish_time'),
            'type' => $request->input('type'),
            'hits' => $request->input('hits'),
            'state' => $request->input('state'),
            'recommend' => $request->input('recommend'),
            'favor' => $request->input('favor'),
            'source_type' => $request->input('source_type')
        ];

        $id = $request->input('id');
        if (is_null($id)) {
            $article = new Article($form);
            $article->save();
            $article->save();
        } else {
            $article = Article::find($id);
            $article->update($form);
        }

        $body = $request->input('body');
        $articleBody = ArticleBody::where('id', $article->id)->first();
        if (is_null($articleBody)) {
            $articleBody = new ArticleBody([
                'id' => $article->id,
                'body' => $body
            ]);

            $articleBody->save();
        } else {
            ArticleBody::where('id', $article->id)->update([
                'body' => $body
            ]);
        }

        //更新分类
        $categories = $request->input('categories', []);
        if (is_array($categories) and count($categories) > 0) {
            ArticleCategory::where('aid', $article->id)->delete();
            $all_cats = [];
            foreach ($categories as $val) {
                array_push($all_cats, ['aid' => $article->id, 'cid' => $val]);
            }

            ArticleCategory::insert($all_cats);
        }

        $this->updateArticleTemplate($article->id);

        return ['success' => 1];
    }

    /**
     * 更新相关静态页
     * @param $id
     */
    private function updateArticleTemplate($id, $type = 'update')
    {
        if (!\numcheck::is_int($id)) {
            return;
        }

        // 生成静态页
        if ($type == 'update') {
            $this->template_updater->update_page('read/' . $id);
        } elseif ($type == 'delete') {
            $this->template_updater->delete_page('read/' . $id);
        }

        $categories = DB::table('article_category')->join('category', 'article_category.cid', '=', 'category.id')
            ->where('article_category.aid', $id)
            ->pluck('category.ename');

        // 生成列表页
        if (!is_null($categories)) {
            foreach ($categories as $category) {
                $this->template_updater->update_page('news/' . $category);
            }
        }
    }

    /**
     * 表单验证
     * @param Request $request
     * @return array
     */
    private function validateForm(Request $request)
    {
        $messages = [
            'title.required' => '请输入标题',
            'title.max' => '标题长度不能超过64个字符',
            'title.min' => '标题长度不能少于2个字符',
            'description.required' => '描述不能为空',
            'description.max' => '描述长度不能超过512个字符',
            'description.min' => '描述长度不能少于2个字符',
            'keywords.max' => '关键词长度最多64个字符',
            'author.max' => '作者长度最多16个字符',
            'image.required' => '图片不能为空',
            'type.required' => '类型不能为空',
            'type.max' => '类型长度不能超过32字符',
            'type.min' => '类型长度不能少于2字符',
            'recommend.integer' => '推荐状态不正确',
            'state.integer' => '状态不正确',
            'favor.integer' => '收藏数不正确',
            'hits.integer' => '点击量不正确',
            'source_type.required' => '原创类型不能为空',
            'publish_time.require' => '发布日期不能为空',
            'publish_time.date' => '发布时间不正确'
        ];
        $rules = [
            'title' => 'required|max:64|min:2',
            'description' => 'required|max:512|min:2',
            'keywords' => 'max:64',
            'author' => 'max:16',
            'image' => 'required',
            'type' => 'required|max:32|min:2',
            'recommend' => 'required|integer',
            'hits' => 'required|integer',
            'favor' => 'required|integer',
            'state' => 'required|integer',
            'publish_time' => 'required|date',
            'source_type' => 'required'
        ];
        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return ['success' => 0, 'msg' => $validator->errors()];
        }

        return ['success' => 1];
    }

    /**
     * 添加来源网站
     * @auth article:update
     * @param Request $request
     */
    public function addOrUpdateSourceSite(Request $request) {
        $site = $request->input('site');
        $old_name = $request->input('old_site');
        $state = $request->input('state');
        $name = empty($old_name)?$site:$old_name;

        if(!is_null($site)) {
            $config = Config::where('key', 'article_source_site')->first();
            $value = json_decode($config->value, true);
            $in = false;

            foreach ($value as $key=>$val) {
                if($val['site'] == $name) {
                    $value[$key]['state'] = $state;
                    $value[$key]['site'] = $site;
                    $in = true;
                    break;
                }
            }

            if(!$in) {
                $value[] = ['site'=>$site, 'state'=>$state];
            }

            $config->value = json_encode($value, JSON_UNESCAPED_UNICODE);
            $config->save();

            return ['success'=>1, 'data'=>$value];
        }
    }

    /**
     * 删除来源网站
     * @auth article:delete
     * @param Request $request
     */
    public function removeSourceSite(Request $request) {
        $site = $request->input('site');
        if(!is_null($site)) {
            $config = Config::where('key', 'article_source_site')->first();
            $value = json_decode($config->value, true);
            $new_value = [];
            foreach ($value as $key=>$val) {
                if($val['site'] !== $site) {
                    $new_value[] = ['site'=>$val['site'], 'state'=>$val['state']];
                }
            }

            $config->value = json_encode($new_value, JSON_UNESCAPED_UNICODE);
            $config->save();

            return ['success'=>1, 'data'=>$new_value];
        }

        return ['success'=>0];
    }

    /**
     * @auth article:all
     * @param Request $request
     * @return array
     */
    public function listBySite(Request $request)
    {
        $pageSize = $request->input('size');
        is_null($pageSize) && !is_numeric($pageSize) && $pageSize = 10;

        $time = $request->input('time');
        $site = $request->input('site');
        $page = $request->input('page');


        $res = DB::table('article')
            ->where('source_site', '=', $site)
            ->where('state', '=', 0)
            ->where('created_at', '>=', $time);

        $count = $res->count();

        return [
            'success' => 1,
            'value' => [
                "list" => $res->forPage($page, $pageSize)->orderBy('created_at')->get(),
                'count' => $count,
                'page' => $page,
                'pageSize' => $pageSize
            ]
        ];
    }

    /**
     * 根据source_id跳转到前端页面
     * @param Request $request
     */
    public function toArticlePage(Request $request) {
        $page = $request->input('page');
        $url = "http://www.yjshare.cn";

        if($page){
            $source_id = md5($page);
            $article = DB::table('article')->where('source_id', $source_id)->select('id')->first();
            if($article) {
                $url = 'http://www.yjshare.cn/blog_'.$article->id.'.html';
            }
        }

        return redirect()->away($url);
    }

    /**
     * 重新下载未成功的图片
     * @auth article:read
     * @param Request $request
     */
    public function redownloadImage(Request $request) {
        $url = $request->input('url');

        if($url) {
            $url = str_replace("/uploads/crawler", "/data/images/uploads", $url);
            $ori = DB::table('image_map')->where('img_path', $url)->select('real_path')->first();
            if($ori)
            {
                $this->kafka->produce('downfile_queue_with_thumb', $ori->real_path);
                return ['success'=>1];
            }

            return ['success'=>0];
        }

        return ['success'=>0];
    }
}