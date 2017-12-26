<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;

class ListController extends Controller
{
    public function lists(Request $request) {
        $type = $request->type;
        if($type) {
            $articles = DB::table('weixin_article')
                ->where('type', $type)
                ->orderBy('publish_time', 'desc')
                ->paginate(20);

            return view('index.search', ['articles'=>$articles, "seo_title"=>$type."_粮叔叔_炜煜稻花香合作社", "seo_description"=>"粮叔叔为您呈现关于".$type."的最新资讯, 搜索结果, 如有需要纯正五常大米,请联系13522168390(刘女士)"]);
        }

        return redirect("/");
    }

    public function baidusearch(Request $request, $id) {
        if($id) {
            $articles = $this->getsearchlist("baidu_hotkey", $id);
            $key = $articles[0]->keyword;
            return view('index.search', ['articles'=>$articles, "seo_title"=>$key."_粮叔叔_炜煜稻花香合作社", "seo_description"=>"粮叔叔为您呈现关于".$key."的最新资讯, 搜索结果, 如有需要纯正五常大米,请联系13522168390(刘女士)"]);
        }

        return redirect("/");
    }

    public function weibosearch(Request $request, $id) {
        if($id) {
            $articles = $this->getsearchlist("weibo_hotkey", $id);
            if(count($articles) > 0) {
		$key = $articles[0]->keyword;
        	return view('index.search', ['articles'=>$articles, "seo_title"=>$key."_粮叔叔_炜煜稻花香合作社", "seo_description"=>"粮叔叔为您呈现关于".$key."的最新资讯, 搜索结果, 如有需要纯正五常大米,请联系13522168390(刘女士)"]);
        }
	    }

        return redirect("/");
    }

    private function getsearchlist($tb, $id) {
        $articles = DB::table('weixin_article')
            ->join($tb, $tb.".keyword", "=", "weixin_article.type")
            ->where($tb.".id", $id)
            ->orderBy('publish_time', 'desc')
            ->select($tb.".keyword", "weixin_article.id", "weixin_article.title", "weixin_article.publish_time", "weixin_article.author")
            ->paginate(20);

        return $articles;
    }

    public function keys(Request $request, $key, $page=1) {
        if($key) {
            $articles = DB::table('keywords_map')
                ->join("weixin_article", "keywords_map.s_id", "=", "weixin_article.id")
                ->where("keywords_map.keyword", $key)
                ->orderBy('weixin_article.publish_time', 'desc')
                ->select("weixin_article.id", "weixin_article.title", "weixin_article.publish_time", "weixin_article.author")
                ->paginate(20);

            return view('index.search', ['articles'=>$articles, "seo_title"=>$key."_粮叔叔_炜煜稻花香合作社", "seo_description"=>"粮叔叔为您呈现关于".$key."的最新资讯, 如有需要纯正五常大米,请联系13522168390(刘女士)"]);
        }

        return redirect("/");
    }

    public function type(Request $request) {
        $keywords = $request->keywords;
        if($keywords) {
            $articles = DB::table('weixin_article')
                ->where('type', $keywords)
                ->paginate(20);

            return view('index.search', ['articles'=>$articles, "seo_title"=>$keywords."_粮叔叔_炜煜稻花香合作社", "seo_description"=>"粮叔叔为您呈现关于".$keywords."的最新资讯, 如有需要纯正五常大米,请联系13522168390(刘女士)"]);
        }

        return redirect("/");
    }

    public function search(Request $request) {
        $keywords = $request->keywords;
        if($keywords) {
            $articles = DB::table('weixin_article')
                ->where('title', 'like', '%'.$keywords.'%')
                ->orWhere('type', 'like', $keywords.'%')
                ->paginate(20);

            return view('index.search', ['articles'=>$articles, "seo_title"=>$keywords."_粮叔叔_炜煜稻花香合作社", "seo_description"=>"粮叔叔为您呈现关于".$keywords."的最新资讯, 如有需要纯正五常大米,请联系13522168390(刘女士)"]);
        }

        return redirect("/");
    }

    public function author(Request $request, $author) {
        if($author) {
            $articles = DB::table('weixin_article')
                ->where('author', $author)
                ->paginate(20);

            return view('index.search', ['articles'=>$articles, "seo_title"=>$author."_粮叔叔_炜煜稻花香合作社", "seo_description"=>"粮叔叔为您呈现关于".$author."的最新资讯, 如有需要纯正五常大米,请联系13522168390(刘女士)"]);
        }

        return redirect("/");
    }
}
