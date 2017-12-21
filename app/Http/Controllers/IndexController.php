<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\FoodService;
use App\User;
use App\Models\Kuaixun;
use App\Models\Search;

class IndexController extends Controller
{
    public function __construct(FoodService $food) {
        $this->food = $food;
    }

    public function index(Request $request) {
        $articles = DB::table('weixin_article')
            ->orderBy("publish_time", 'desc')
            ->take(4)
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
        $assign['hotkey'] = $this->hotkey($request);

	    $assign['weibos'] = $this->weibo($request);
        $assign['keywords'] = $this->keywords($request, 55);

        //快讯
        $kuaixun = new Kuaixun();
        $date = $request->input('st', null);
        $ret = $kuaixun->getKuaixun(1, 10, $date);
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
        $seo_description = "粮叔叔提供". $article->type ."的资讯新闻，打造一流的" . $article->type ."的服务提供商。";
        return view('index.detail', ['article'=>$article, 'hot'=>$hot, 'favor'=>$favor, 'related'=>$related, 'latest'=>$latest, "seo_title"=>$seo_title, "seo_description"=>$seo_description]);
    }

    public function search(Request $request) {
        $keywords = $request->keywords;
        $articles = DB::table('weixin_article')
            ->where('title', 'like', '%'.$keywords.'%')
            ->orWhere('type', 'like', $keywords.'%')
            ->paginate(20);

        return view('index.search', ['articles'=>$articles]);
    }

    public function type(Request $request) {
        $keywords = $request->keywords;
        $articles = DB::table('weixin_article')
            ->where('type', $keywords)
            ->paginate(20);

        return view('index.search', ['articles'=>$articles]);
    }

    public function img(Request $request)
    {
        $name = $request->input('ori', null);
        if(!is_null($name)) {
            $hash_dir = substr(base_convert(md5($name), 16, 10), 0, 5) % 99;

            preg_match("/mmbiz_.*\/(.*)\//", $name, $match);
            $img_name = $name;
            if(count($match) > 1) {
                $img_name = $match[1];
            }
            else {
                foreach([":", "/", ">", "?", "=", "."] as $rep) {
                    $img_name = str_replace($rep, "", $img_name);
                }
            }

            $tmp_dir = "images/".$hash_dir;
            if(!file_exists($tmp_dir)) {
                mkdir($tmp_dir);
            }

            $tmp_name = $tmp_dir."/".$img_name;

            $offset = 30*60*60*24; // cache 1 month
            if(file_exists($tmp_name))
            {
                return response(file_get_contents($tmp_name), 200, [
                    'Content-Type' => 'image/png',
                    "Cache-Control"=>" public",
                    "Pragma" => "cache",
                    "Expires" => gmdate("D, d M Y H:i:s", time() + $offset)." GMT"
                ]);
            }

            $img = file_get_contents($name);
            file_put_contents($tmp_name, $img);
            return response($img, 200, [
                'Content-Type' => 'image/png',
                "Cache-Control"=>" public",
                "Pragma" => "cache",
                "Expires" => gmdate("D, d M Y H:i:s", time() + $offset)." GMT"
            ]);
        }
        else {
            return "图片地址不合法";
        }
    }

    public function hotkey(Request $request) {
        $search = Search();
        $all_keys = $search->hotSearch();
        return $all_keys;
    }

    public function weibo(Request $request) {
        $weibo = DB::table('weibo')->orderBy("id", 'desc')
            ->take(3)
            ->get();

        return $weibo;
    }

    public function keywords(Request $request, $num=20) {
        $num = $request->input("num", $num);
        $skip = rand(1, 200);
        $last_week = date("Y-m-d H:i:s", strtotime("-5 days"));
        $hotkeywords = DB::table("keywords_map")
            ->where("created_time", ">=", $last_week)
            ->groupBy("keyword")
            ->select(DB::Raw("count(*) as cou, keyword"))
            ->orderBy("cou", "desc")
            ->skip($skip)
            ->take($num)
            ->get();

        return $hotkeywords;
    }
}
