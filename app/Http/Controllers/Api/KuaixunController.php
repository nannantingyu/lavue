<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kuaixun;

class KuaixunController extends Controller
{
    public function __construct()
    {
        $this->default_enable_days = 2;
    }

    /**
     * 获取财经快讯（来源：jin10.com）
     * @param Request $request
     * @param p ['pc', 'app]：请求来自pc还是app
     * @param st：开始时间
     * @param et：结束时间
     * @param page：页数
     * @param num：每页请求的条数，最大20
     * @return array
     */
    public function getkx(Request $request) {
        $kuaixun = new Kuaixun();
        $platform = $request->input('p', 'pc');
        $date = $request->input('st', null);
        $enddate = $request->input('et', null);
        $page_num = $request->input('num', 10);
        if($page_num > 20) {
            $page_num = 20;
        }

        list($date, $enddate) = $this->getDefaultDate($date, $enddate);
        $ret = $kuaixun->getKuaixun($request->input('page', 1), $page_num, $date, $enddate);

        foreach ($ret['ret'] as $key=>$val) {
            if($this->inWords($val->body, ['jin10.com', 'fx678.com', 'wallstreetcn.com', '金十']))
                unset($ret['ret'][$key]);

            elseif ($platform == 'app')
                $ret['ret'][$key]->body = strip_tags($val->body);
        }

        return ['success'=>1, 'value'=>array_values($ret['ret']), 'count'=>$ret['count']];
    }

    /**
     * 判断句子中是否含有非法字符
     * @param $str
     * @param $keys
     * @return bool
     */
    private function inWords($str, $keys) {
        foreach($keys as $key) {
            if (strstr($str, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 获取默认的开始和结束时间
     * @param $stdate
     * @param $enddate
     * @return array
     */
    private function getDefaultDate($stdate, $enddate) {
        if(is_null($stdate)) {
            if(!is_null($enddate)) {
                $stdate = date("Y-m-d H:i:s", strtotime($enddate) - 3600*24*$this->default_enable_days);
            }
            else {
                $stdate = date("Y-m-d H:i:s", strtotime("-$this->default_enable_days days"));
            }
        }

        return [$stdate, $enddate];
    }

    /**
     * 获取合并的快讯
     * @param Request $request
     */
    public function getUnionKx(Request $request) {
        $kx = $this->getkx($request);
        $bkx = $this->getBlockKx($request);

        $ret = [];
        if($kx['success'] === 1 and is_array($kx['value'])) {
            $ret = array_merge($ret, $kx['value']);
        }

        if($bkx['success'] === 1 and is_array($bkx['value'])) {
            $ret = array_merge($ret, $bkx['value']);
        }

        usort($ret, function($a, $b) {
            return $a->publish_time < $b->publish_time;
        });

        $num = $request->input('num', 10);
        if(count($ret) > $num) {
            $ret = array_slice($ret, $num);
        }

        return ['success'=>1, 'value'=>$ret];
    }

    /**
     * 获取区块链快讯（来源：金色财经）
     * @param Request $request
     * @param p ['pc', 'app]：请求来自pc还是app
     * @param st：开始时间
     * @param et：结束时间
     * @param page：页数
     * @param num：每页请求的条数，最大20
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBlockKx(Request $request) {
        $platform = $request->input('p', 'pc');
        $date = $request->input('st');
        $enddate = $request->input('et');

        $page_num = $request->input('num', 5);
        if($page_num > 20) {
            $page_num = 20;
        }

        list($date, $enddate) = $this->getDefaultDate($date, $enddate);

        $result = DB::table('kuaixun_block')
            ->orderBy('publish_time', 'desc')
            ->select('id', 'body', 'publish_time', 'importance', 'created_at');

        if($date) {
            $result = $result->where('publish_time', '>', $date);
        }

        if($enddate) {
            $result = $result->where('publish_time', '<', $enddate);
        }

        $result = $result->paginate($page_num)->toArray();

        $data = [];
        foreach ($result['data'] as $key=>$val) {
            $val->importance = $val->importance > 3?1:0;
            $val->source_site='block';
            if(!$this->inWords($val->body, ['金色']))
                if ($platform == 'app')
                    $val->body = strip_tags($val->body);

                array_push($data, $val);
        }

        return ['success'=>1, 'value'=>array_values($data)];
    }
}
