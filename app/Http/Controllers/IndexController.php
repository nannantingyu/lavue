<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\FoodService;
use App\User;

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
            "healthy"   =>  ["name"=>"健康", "num"=>6],
            "changping"   =>  ["name"=>"昌平", "num"=>5],
            "huilongguan"   =>  ["name"=>"回龙观", "num"=>5],
            "food"   =>  ["name"=>"饮食", "num"=>5],
            "exercise"   =>  ["name"=>"锻炼", "num"=>5],
            "house"   =>  ["name"=>"房价", "num"=>5],
            "live"  =>  ['name'=>"生活", "num"=>2]
        ];

        //后门，清理缓存
        if($request->tutu) {
            Cache::flush();
        }

        $assign = [];
        foreach($type_index as $key=>$val) {
            $assign[$key] = Cache::remember($key, 120, function() use($val) {
                return DB::table('weixin_article')
                    ->where("type", $val)
                    ->orderBy("publish_time", 'desc')
                    ->select('type', 'title','id','publish_time', 'updated_time', 'description', 'from_user', 'image')
                    ->take($val['num'])
                    ->get();
            });
        }

        $assign['articles'] = $articles;

        $assign['hotkey'] = $this->hotkey($request);
        return view("index.index", $assign);
    }

    public function detail(Request $request) {
        $id = $request->id;
        $article = DB::table('weixin_article')->where('id', $id)->first();

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

    public function lists(Request $request) {
        $type = $request->type;
        if($type) {
            $articles = DB::table('weixin_article')
                ->where('type', $type)
		->orderBy('publish_time', 'desc')
                ->paginate(20);

            return view('index.search', ['articles'=>$articles]);
        }

        return redirect("/");
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
        $num = $request->input('num', 20);

        $hotkey = DB::table("weibo_hotkey")->where("state", 1)
            ->orderBy('time', 'desc')
            ->orderBy('order', 'asc')
            ->take($num/2)
            ->get()
            ->toArray();

        $hotkey2 = DB::table("baidu_hotkey")->where("state", 1)
            ->orderBy('time', 'desc')
            ->orderBy('order', 'asc')
            ->take($num/2)
            ->get()
            ->toArray();

        $all_keys = array_merge($hotkey, $hotkey2);

        usort($all_keys, function($a, $b){
            return $a->time < $b->time;
        });

        return $all_keys;
    }
}
