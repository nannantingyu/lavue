<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Search extends Model
{
    public function hotSearch($page=1, $num=20) {
        $hotkey = DB::table("weibo_hotkey")->where("state", 1)
            ->orderBy('time', 'desc')
            ->orderBy('order', 'asc')
            ->take($num/2)
            ->select('id', 'time', 'keyword', 'order')
            ->get()
            ->toArray();

        array_map(function($val) {
            $val->site = 'weibo';
            return $val;
        }, $hotkey);

        $hotkey2 = DB::table("baidu_hotkey")->where("state", 1)
            ->orderBy('time', 'desc')
            ->orderBy('order', 'asc')
            ->take($num/2)
            ->select('id', 'time', 'keyword', 'order')
            ->get()
            ->toArray();

        array_map(function($val) {
            $val->site = 'baidu';
            return $val;
        }, $hotkey2);

        $all_keys = array_merge($hotkey, $hotkey2);

        usort($all_keys, function($a, $b){
            return $a->time < $b->time;
        });

        return $all_keys;
    }

    public function search() {
        $weibo = DB::table("weibo_hotkey")->where("state", 1)
            ->orderBy('time', 'desc')
            ->orderBy('order', 'asc')
            ->select('id', 'time', 'keyword', 'order')
            ->paginate(30);

        $baidu = DB::table("baidu_hotkey")->where("state", 1)
            ->orderBy('time', 'desc')
            ->orderBy('order', 'asc')
            ->select('id', 'time', 'keyword', 'order')
            ->paginate(30);

        return ['weibo'=>$weibo, 'baidu'=>$baidu];
    }

    public function detail($id) {
        $table = strpos($id, "fx") === 0?"fx678_kuaixun": strpos($id, "ji") === 0?"jin10_kuaixun":"wallstreetcn_kuaixun";
        $id = substr($id, 2);
        return DB::table($table)->where("id", $id)->first();
    }
}