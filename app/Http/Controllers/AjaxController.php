<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
{
    public function incre(Request $request) {
        $id = $request->id;
        DB::table('weixin_article')->where('id', $id)->increment('favor');
    }

    public function baidu_tuisong(Request $request) {
        $new_articles = DB::table("weixin_article")
            ->where("created_time", ">=", date("Y-m-d 00:00:00", time()-3600*24))
            ->where("created_time", "<", date("Y-m-d 00:00:00", time()))
            ->whereNotNull("body")
            ->whereNotNull("title")
            ->select("id", "created_time")
            ->get();

        $urls = [];
        foreach($new_articles as $val) {
            $urls[] = "http://www.yjshare.cn/blog_" . $val->id;
        }

        $api = 'http://data.zz.baidu.com/urls?site=www.yjshare.cn&token=NzMJMFbCjywQOoFz';
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        echo $result;
    }

    public function body_src_repl(Request $request) {
        $from = $request->from? $request->from:0;
        $all_article = DB::table("weixin_article")
            ->select("id", "body")
            ->skip($from)
            ->take(50)
            ->get();

        foreach($all_article as $article) {
            $body = preg_replace('/\ssrc="(.*?)"\sdata-src="\\\g<1>"/', ' src="\\1" data-src="\\1"', $article->body);
            DB::table('weixin_article')->where("id", $article->id)->update(['body'=>$body]);

            echo($article->id . " has modyfied.<br/>");
        }
    }
}
