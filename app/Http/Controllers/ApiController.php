<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Search;
use App\Models\Keywords;
use App\Models\Kuaixun;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
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
        $search = new Search();

        $page = $request->input('page', 1);
        $num = $request->input('num', 1);
        $all_keys = $search->hotSearch($page, $num);
        return $all_keys;
    }

    public function keywords(Request $request) {
        $keynum = $request->input('num', 20);
        $skip = $request->input('skip', rand(1, 200));
        $starttime = $request->input('start', date("Y-m-d H:i:s", strtotime("-5 days")));

        $key = new Keywords();
        $assign['keywords'] = $key->hotkeywords($starttime, $skip, $keynum);
    }

    public function hotweibo(Request $request) {
        $num = $request->input('num', 3);
        $weibo = DB::table('weibo')->orderBy("id", 'desc')
            ->take($num)
            ->get();

        return $weibo;
    }

    public function getkx(Request $request) {
        $kuaixun = new Kuaixun();
        $date = $request->input('st', null);
        $page = $request->input('page', 1);
        $num = $request->input('num', 10);

        $ret = $kuaixun->getKuaixun($page, $num, $date);
        return $ret;
    }
}
