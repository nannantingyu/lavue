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

        return view("house/residential", ['areas'=>$areas, 'residential'=>$residential]);
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
        return view('house/history');
    }

    public function crawl(Request $request, $name) {
        if($name) {
            shell_exec("cd /d E:/Captain/spider-scrapy/crawl && D:/soft/Python2.7/Scripts/scrapy.exe crawl crawl_house_history -a args=name:$name");
            return "爬取成功";
        }
    }

}
