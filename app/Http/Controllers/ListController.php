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

            return view('index.search', ['articles'=>$articles]);
        }

        return redirect("/");
    }

    public function baidusearch(Request $request, $id) {
        if($id) {
            $articles = $this->getsearchlist("baidu_hotkey", $id);
            return view('index.search', ['articles'=>$articles]);
        }

        return redirect("/");
    }

    public function weibosearch(Request $request, $id) {
        if($id) {
            $articles = $this->getsearchlist("weibo_hotkey", $id);
            return view('index.search', ['articles'=>$articles]);
        }

        return redirect("/");
    }

    private function getsearchlist($tb, $id) {
        $articles = DB::table('weixin_article')
            ->join($tb, $tb.".keyword", "=", "weixin_article.type")
            ->where($tb.".id", $id)
            ->orderBy('publish_time', 'desc')
            ->select("weixin_article.id", "weixin_article.title", "weixin_article.publish_time", "weixin_article.author")
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

            return view('index.search', ['articles'=>$articles]);
        }

        return redirect("/");
    }

    public function type(Request $request) {
        $keywords = $request->keywords;
        if($keywords) {
            $articles = DB::table('weixin_article')
                ->where('type', $keywords)
                ->paginate(20);

            return view('index.search', ['articles'=>$articles]);
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

            return view('index.search', ['articles'=>$articles]);
        }

        return redirect("/");
    }

    public function author(Request $request, $author) {
        if($author) {
            $articles = DB::table('weixin_article')
                ->where('author', $author)
                ->paginate(20);

            return view('index.search', ['articles'=>$articles]);
        }

        return redirect("/");
    }
}
