<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kuaixun;
use App\Http\Controllers\IndexController;
use App\Models\Search;

class KuaixunController extends Controller
{
    public function getkx(Request $request) {
        $kuaixun = new Kuaixun();
        $date = $request->input('st', null);
        $ret = $kuaixun->getKuaixun($request->input('page'), $request->input('num'), $date);

        return ['success'=>1, 'value'=>array_values($ret)];
    }

    public function kuaixun(Request $request) {
        $kuaixun = new Kuaixun();
        $date = $request->input('st', null);
        $ret = $kuaixun->getKuaixun($request->input('page', 1), $request->input('num', 20), $date);

        return view("index.kuaixun", ['kx'=>$ret, 'seo_title'=>'最新、最快、最全的财经快讯-粮叔叔', "seo_keywords"=>"快讯,财经", "seo_description"=>"粮叔叔提供最新、最快、最全的财经快讯，为你的投资理财保驾护航"]);
    }

    public function kuaixun_detail(Request $request, $id) {
        if($id) {
            $kuaixun = new Kuaixun();
            $kx = $kuaixun->detail($id);

            $search = new Search();
            $hotsearch = $search->hotSearch();

            $hot = DB::table('weixin_article')
                ->orderBy('hits', 'desc')->take(20)
                ->select('id', 'title', 'publish_time', 'created_time')
                ->get();

            $favor = DB::table('weixin_article')
                ->orderBy('favor', 'desc')
                ->select('id', 'title', 'publish_time', 'created_time')
                ->take(5)->get();

            $latest = DB::table('weixin_article')
                ->orderBy('publish_time', 'desc')
                ->select('id', 'title', 'publish_time', 'created_time')
                ->take(5)->get();

            $params = [
                "seo_title"=>mb_substr(strip_tags($kx->body), 0, 30)."-粮叔叔",
                "seo_description"=>"粮叔叔为您呈现关于的最新资讯, 搜索结果, 如有需要纯正五常大米,请联系13522168390(刘女士)",
                'kx'=>$kx, 'hot'=>$hot, 'favor'=>$favor, 'hotsearch'=>$hotsearch, 'latest'=>$latest
            ];

            return view("index.kx_detail", $params);
        }
    }
}
