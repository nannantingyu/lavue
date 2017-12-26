<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\FoodService;
use App\User;
use App\Models\Kuaixun;
use App\Models\Search;
use App\Models\Keywords;

class IndexController extends Controller
{
    public function __construct(FoodService $food) {
        $this->food = $food;
    }

    public function index(Request $request) {
        // 轮播文章
        $articles = DB::table('weixin_article')
            ->orderBy("publish_time", 'desc')
            ->take(5)
            ->get();

        $type_index = [
            "rice"  =>  ["name"=>"五常大米", "num"=>6],
            "beijing"  =>  ["name"=>"北京", "num"=>6],
            "china"  =>  ["name"=>"中国", "num"=>6],
        ];

        //后门，清理缓存
        if($request->tutu) {
            Cache::flush();
        }

        $assign = [];

        // 热门文章
        foreach($type_index as $key=>$val) {
            $assign[$key] = Cache::remember($key, 120, function() use($val) {
                return DB::table('weixin_article')
                    ->join('keywords_map', 'weixin_article.id', '=', 'keywords_map.s_id')
                    ->where("keywords_map.keyword", $val['name'])
                    ->orderBy("weixin_article.publish_time", 'desc')
                    ->select('weixin_article.title','weixin_article.id','weixin_article.publish_time', 'weixin_article.updated_time', 'weixin_article.description', 'weixin_article.author', 'weixin_article.image')
                    ->take($val['num'])
                    ->get();
            });
        }

        $assign['articles'] = $articles;

        // 热门搜索
        $search = new Search();
        $assign['hotkey'] = $search->hotSearch();

        // 热门微博
        $hotweibo = DB::table('weibo')->orderBy("id", 'desc')
            ->take(3)
            ->get();
	    $assign['weibos'] = $hotweibo;

        // 热门词汇
        $keynum = 50;
        $skip = rand(1, 200);
        $starttime = date("Y-m-d H:i:s", strtotime("-5 days"));

        $key = new Keywords();
        $assign['keywords'] = $key->hotkeywords($starttime, $skip, $keynum);

        //快讯
        $kuaixun = new Kuaixun();
        $date = $request->input('st', null);
        $ret = $kuaixun->getKuaixun(1, 8, $date);
        $assign['kuaixun'] = $ret;

        return view("index.index", $assign);
    }

    public function detail(Request $request) {
        $id = $request->id;
        $article = DB::table('weixin_article')
            ->join('weixin_article_detail', 'weixin_article.id', '=', 'weixin_article_detail.id')
            ->where('weixin_article.id', $id)
            ->select("weixin_article.*", "weixin_article_detail.body")
            ->first();

        if(!$article) {
            return redirect("/");
        }

        $hot = DB::table('weixin_article')
            ->orderBy('hits', 'desc')->take(5)
            ->select('id', 'title', 'publish_time', 'created_time')
            ->get();
        $related = DB::table('weixin_article')
            ->where('type', $article->type)
	        ->select('id', 'title', 'publish_time', 'created_time')
            ->orderBy('hits', 'desc')->take(5)->get();

        $favor = DB::table('weixin_article')
            ->orderBy('favor', 'desc')
	        ->select('id', 'title', 'publish_time', 'created_time')
            ->take(5)->get();

        $latest = DB::table('weixin_article')
            ->orderBy('publish_time', 'desc')
	        ->select('id', 'title', 'publish_time', 'created_time')
            ->take(5)->get();

        $seo_title = $article->title . "-粮叔叔";
        $seo_description = "粮叔叔提供【". $article->type ."】的资讯新闻";
        $seo_keywords = DB::table('keywords_map')->where('s_id', $id)->pluck("keyword")->toArray();
        $seo_keywords = implode(",", $seo_keywords);
        return view('index.detail', ['article'=>$article, 'hot'=>$hot, 'favor'=>$favor, 'related'=>$related, 'latest'=>$latest, "seo_title"=>$seo_title, "seo_description"=>$seo_description, "seo_keywords"=>$seo_keywords]);
    }

    public function weibo(Request $request) {
        $weibos = DB::table('weibo')->orderBy("id", 'desc')
            ->paginate(10);

        $search = new Search();
        $hotsearch = $search->hotSearch();

        return view('index.weibo', ['weibos'=>$weibos, 'hotsearch'=>$hotsearch, "seo_title"=>"微博好玩的有趣的-粮叔叔", "seo_description"=>"微博上有什么好玩的东西呀, 你的爱豆最近在干嘛呢，快点关注粮叔叔来看看吧！", "seo_keywords"=>"热搜,热门微博,微博,爱豆,有趣,有料"]);
    }

    public function hotsearch(Request $request) {
        // 热门搜索
        $search = new Search();
        return view('index.hotsearch', ['search'=>$search->search(), "seo_title"=>"看看大家都在搜什么-粮叔叔", "seo_description"=>"最近有什么好玩的事情呢，快点关注粮叔叔吧！", "seo_keywords"=>"热搜,热门搜索,百度热搜,微博热搜"]);
    }
}
