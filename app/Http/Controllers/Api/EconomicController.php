<?php
namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EconomicController extends Controller {
    public function getcftc(Request $request) {
        $cftc = DB::table("cftc")
            ->whereIn('cftc_name', ['黄金', '欧元', '日元', '美元', '原油', '白银'])
            ->orderBy("publish_time", "desc")
            ->take(6)
            ->get()
            ->toArray();

        $has = [];
        foreach ($cftc as $key=>$val) {
            if(in_array($val->cftc_name, $has)) {
                unset($cftc[$key]);
            }
            else {
                $has[] = $val->cftc_name;
            }
        }

        return $cftc;
    }

    public function getcftc_app(Request $request) {
        $cftc = DB::table("cftc")
            ->whereIn('cftc_name', ['黄金', '欧元', '日元', '美元', '原油', '白银'])
            ->orderBy("publish_time", "desc")
            ->take(6)
            ->get()
            ->toArray();

        $has = [];
        foreach ($cftc as $key=>$val) {
            if(in_array($val->cftc_name, $has)) {
                unset($cftc[$key]);
            }
            else {
                $has[] = $val->cftc_name;
            }
        }

        $tp = $request->input('tp');
        $ret = $cftc;
        if(!is_null($tp)) {
            $ret = ['value'=>$cftc, 'success'=>1];
        }

        return response()->json($ret);
    }

    public function getstock(Request $request) {
        $type_name_map = [
            1 => "ETF黄金",
            2 => "COMEX黄金",
//            3 => "央行黄金储备",
            4 => "ETF白银",
            5 => "COMEX白银"
        ];

        $num = $request->input("num", 10);
        $need_chubei = $request->input("cb", false);
        if($need_chubei) {
            $type_name_map[3] = "央行黄金储备";
        }

        $sql = "(select * from crawl_stock where type = 1 order by publish_time desc limit 0, $num) ";

        foreach($type_name_map as $key=>$val) {
            if ($key != 1) {
                $sql .= "union (select * from crawl_stock where type = $key order by publish_time desc limit 0, $num) ";
            }
        }

        $stock = DB::connection()->select($sql);
        foreach ($stock as $key=>$val) {
            $stock[$key]->name = $type_name_map[$val->type];
        }

        return $stock;
    }

    public function getssi(Request $request) {
        $pair = $request->input("pair");
        $result = DB::table("fxssi")
            ->where("created_at", ">=", date("Y-m-d 00:00:00"));
        if($pair) {
            $result = $result->where("pair", $pair);
        }

        $result = $result->get();
        foreach($result as $key=>$val) {
            if($val->broker == 'average') {
                $result[$key]->broker = '平均';
            }
        }
        return $result;
    }
}