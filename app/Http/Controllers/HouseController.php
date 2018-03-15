<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Common\Pinyinfirstchar;

class HouseController extends Controller
{
    public function residential(Request $request) {
        $areas = ["北辰区", "和平区", "河西区", "宝坻区", "南开区", "蓟县", "静海", "宁河", "河北", "河东", "红桥", "滨海新区", "西青", "津南", "东丽", "武清"];

        $key = Redis::keys("area:".$areas[0]);
        $residential = [];
        if(count($key) > 0) {
            $residential_json = Redis::smembers("area:".$areas[0]);
            foreach ($residential_json as $val) {
                $r = explode(":", $val);
                $residential[] = [
                    "id"=>$r[0],
                    "name"=>$r[1]
                ];
            }
        }
        else {
            $residential_all = DB::table("anjuke_residential")
                ->where("area", "!=", "天津周边")
                ->select("residential", "area", "residential_id", "build_year", "residential_url")
                ->get()
                ->toArray();

            foreach ($residential_all as $re) {
                if($re->area == $areas[0]) {
                    $residential[] = $re;
                }

                Redis::sadd("area:".$re->area, $re->residential_id.":".$re->residential);
                Redis::set("residential:".$re->residential_id, json_encode($re));
            }
        }


        $seo_title = "天津小区历史房价-粮叔叔";
        $seo_description = "粮叔叔提供【天津各个小区的历史房价】，如需别的房价信息，请联系939259192@qq.com";
        $seo_keywords = "天津小区房价,历史房价,房价,小区";
        return view("house/residential", ['areas'=>$areas, 'residential'=>$residential, "seo_title"=>$seo_title, "seo_description"=>$seo_description, "seo_keywords"=>$seo_keywords]);
    }

    public function getAreaResidential(Request $request) {

        $area = $request->input("area", "北辰区");
        $key = Redis::keys("area:".$area);
        $residential = [];
        $ret = [];

        $py = new pinyinfirstchar();

        if(count($key) > 0) {
            $residential_json = Redis::smembers("area:".$area);
            foreach ($residential_json as $val) {
                $r = explode(":", $val);
                $residential[] = [
                    "id" => $r[0],
                    "name" => $r[1],
                    "word" => $py->getFirstchar($r[1])
                ];
            }
        }

        foreach ($residential as $key=>$val) {
            $ret[$val['word']][] = $val;
        }

        return response()->json($ret);
    }

    public function history(Request $request) {
        $residential_id = $request->input("rid");
        if(!is_null($residential_id)) {
            $rkey = "residential:".$residential_id;
            $history_data = Redis::get($rkey);
            $history_data = json_decode($history_data, true);
            if(!isset($history_data['data']) || empty($history_data['data'])) {
                $history_data_db = DB::table("house_history")
                    ->where("residential_id", $residential_id)
                    ->orderBy("year", "asc")
                    ->orderBy("month", "asc")
                    ->select("year", "month", "price")
                    ->get();

                if(count($history_data_db) > 0) {
                    $history_data['data'] = $history_data_db;
                    Redis::set($rkey, json_encode($history_data));
                }
                else{
                    return [
                        "state" => 0,
                        "message" => "暂无数据，可以进行抓取，请点击抓取"
                    ];
                }
            }

            return ['state' => 1, "data" => $history_data];
        }
    }

    public function crawl(Request $request, $name) {
        if($name) {
            shell_exec("cd /d E:/Captain/spider-scrapy/crawl && D:/soft/Python2.7/Scripts/scrapy.exe crawl crawl_house_history -a args=name:$name");
            return [
                "state" => 1,
                "message" => "爬取成功"
            ];
        }
    }

}
