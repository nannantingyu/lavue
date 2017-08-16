<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\FoodService;

class IndexController extends Controller
{
    public function __construct(FoodService $food) {
        $this->food = $food;
    }

    public function index(Request $request) {
        $name = "nannantingyu";
        $password = "abc123";

        $articles = DB::table('weixin_article')
            ->whereNotNull('body')
            ->orderBy("publish_time", 'desc')
            ->take(10)
            ->get();

        $type_index = [
            "rice"  =>  ["name"=>"五常大米", "num"=>10],
            "live"  =>  ["name"=>"生活", "num"=>10],
            "healthy"   =>  ["name"=>"健康", "num"=>10],
            "changping"   =>  ["name"=>"昌平", "num"=>5],
            "huilongguan"   =>  ["name"=>"回龙观", "num"=>5],
            "food"   =>  ["name"=>"饮食", "num"=>5],
            "exercise"   =>  ["name"=>"锻炼", "num"=>5],
            "house"   =>  ["name"=>"房", "num"=>5],
        ];

        $assign = [];
        foreach($type_index as $key=>$val) {
            $assign[$key] = DB::table('weixin_article')
                ->whereNotNull('body')
                ->where("type", $val)
                ->orderBy("publish_time", 'desc')
                ->take(10)
                ->get();
        }

        $assign['articles'] = $articles;
        return view("index.index", $assign);
    }

    public function detail(Request $request) {
        $id = $request->id;
        $article = DB::table('weixin_article')->where('id', $id)->first();

        $hot = DB::table('weixin_article')->orderBy('hits', 'desc')->take(5)->get();
        $related = DB::table('weixin_article')
            ->where('type', $article->type)
            ->orderBy('hits', 'desc')->take(5)->get();

        $favor = DB::table('weixin_article')
            ->orderBy('favor', 'desc')
            ->take(5)->get();

        $latest = DB::table('weixin_article')
            ->orderBy('publish_time', 'desc')
            ->take(5)->get();
        return view('index.detail', ['article'=>$article, 'hot'=>$hot, 'favor'=>$favor, 'related'=>$related, 'latest'=>$latest ]);
    }

    public function search(Request $request) {
        $keywords = $request->keywords;
        $articles = DB::table('weixin_article')
            ->where('title', 'like', '%'.$keywords.'%')
            ->orWhere('type', 'like', '%'.$keywords.'%')
            ->orWhere('body', 'like', '%'.$keywords.'%')
            ->whereNotNull('body')
            ->paginate(20);

        return view('index.search', ['articles'=>$articles]);
    }
}
