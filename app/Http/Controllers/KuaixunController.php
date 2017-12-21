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

        return view("index.kuaixun", ['kx'=>$ret]);
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

            return view("index.kx_detail", ['kx'=>$kx, 'hot'=>$hot, 'favor'=>$favor, 'hotsearch'=>$hotsearch, 'latest'=>$latest]);
        }
    }
}
