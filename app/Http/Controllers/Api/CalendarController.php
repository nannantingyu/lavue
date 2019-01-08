<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EconomicCalendar;
use App\Models\EconomicJiedu;
use App\Models\EconomicEvent;
use App\Models\EconomicHoliday;

class CalendarController extends Controller
{
    public function getcjdatas(Request $request) {
        $date = $request->input("d", date("Y-m-d"));
        $pcOrm = $request->input('pcOrm', 'pc');

        $weekData = $this->getWeekData($request);
        $cjdata = $this->getDates($request);

        if($pcOrm == 'pc') {
            $all_cj = [];
            foreach($cjdata as $data) {
                $all_cj[substr($data['stime'], 11, 5).'_'.$data['country_cn']]['_ch'][] = $data;
            }
        }
        else {
            $all_cj = $cjdata;
        }

        $sjdata = $this->getcjevent($request);
        $hjdata = $this->getcjholiday($request);

        $ret['date'] = $weekData;
        $ret['cjdata'] = $all_cj;
        $ret['sjdata'] = $sjdata;
        $ret['hdata'] = $hjdata;

        return $ret;
    }

    /**
     * 获取国家
     * @param $request
     * @return array|null|string
     */
    private function getcountry($request) {
        $country = $request->input("country");
        if($country == 'all') {
            return null;
        }

        $country = empty($country)?'美国,欧元区,德国,英国,法国,中国,日本':$country;
        $country = explode(",", $country);

        return $country;
    }

    public function getcjevent(Request $request) {
        $date = $this->getDateInparams($request);
        $country = $this->getcountry($request);

        $sj_data = EconomicEvent::where('time', ">=", $date['st'])
            ->where('time', '<=', $date['et']);

        if(!is_null($country)) {
            $sj_data = $sj_data->whereIn('country', $country);
        }

        $sj_data = $sj_data->select("id", 'time as event_time', 'country', 'city as area', 'importance', 'event as event_desc')
            ->get()
            ->toArray();

        $sj_data = array_map(function ($dt) {
            $dt['event_time'] = substr($dt['event_time'], 11, 5);
            $dt['time_flag'] = 1;
            return $dt;
        }, $sj_data);

        return $sj_data;
    }

    public function getcjholiday(Request $request) {
        $date = $this->getDateInparams($request);
        $country = $this->getcountry($request);
        $hj_data = EconomicHoliday::where('time', ">=", $date['st'])
            ->where('time', '<=', $date['et']);

        if(!is_null($country)) {
            $hj_data = $hj_data->whereIn('country', $country);
        }

        $hj_data = $hj_data->select("id", 'time as event_time', 'country', 'market as area', 'detail as event_desc')
            ->get()
            ->toArray();

        $hj_data = array_map(function($dt){
            $dt['event_time'] = substr($dt['event_time'], 5, 5);
            $dt['importance'] = 3;
            $dt['time_flog'] = null;

            return $dt;
        }, $hj_data);

        return $hj_data;
    }

    public function getDates(Request $request) {
        $date = $this->getDateInparams($request);
        $limit = $request->input("limit");
        $reg = $request->input("reg");
        $ci = $request->input("ci", 0);
        $country = $this->getcountry($request);
        $calendars = EconomicCalendar::where('pub_time', ">=", $date['st'])
            ->where('pub_time', '<=', $date['et']);

        if(!is_null($country)) {
            $calendars = $calendars->whereIn('country', $country);
        }

        $calendars = $calendars->orderBy('pub_time', 'asc');
        if(!empty($reg)) {
            $calendars = $calendars->where('country', $reg);
        }

        if($ci == 1) {
            $calendars = $calendars->where('importance', '>', 2);
        }
        elseif($ci == 2) {
            $calendars = $calendars->where('pub_time', '>', date('Y-m-d H:i:s'));
        }

        if(!is_null($limit)) {
            $calendars = $calendars->take($limit);
        }

        $calendars = $calendars->get()->toArray();
        return $this->dataToData($calendars);
    }

    public function getPastorWillFd(Request $request) {
        $date = $this->getDateInparams($request);
        $now = date('Y-m-d H:i:s');

        $limit1 = $request->input('limit1');
        $limit2 = $request->input('limit2');

        $country = $this->getcountry($request);
        $today = EconomicCalendar::where('pub_time', ">=", $date['st'])
            ->where('pub_time', '<=', $date['et']);
        if(!is_null($country)) {
            $today = $today->whereIn('country', $country);
        }

        $today = $today->orderBy("pub_time", "asc")
            ->get()
            ->toArray();

        $all_past = [];
        $all_will = [];
        foreach($today as $data) {
            if($data['pub_time'] <= $now) {
                $all_past[] = $data;
            }
            else{
                $all_will[] = $data;
            }
        }

        if(count($all_past) < $limit1) {
            $limit2 += $limit1 - count($all_past);
        }

        if(count($all_will) < $limit2) {
            $limit1 += $limit2 - count($all_will);
        }

        $past = array_slice($all_past, -$limit1);
        $will = array_slice($all_will, 0, $limit2);

        return $this->dataToData(array_merge($past, $will));
    }

    /**
     * 获取一周的财经日历
     * @param pcOrm pc|m
     * @param d 时间
     * @return mixed
     */
    public function getWeekData(Request $request) {
        $pcOrm = $request->input('pcOrm', 'pc');
        $date = strtotime($request->input('d', date('Y-m-d')));
        if(!$date) {
            $date = time();
        }

        $weeks = ['星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期日'];
        $all_weeks = [];

        $week_now = date('w', $date);
        $week_now = $week_now == 0?7:$week_now;

        if($pcOrm == 'pc') {
            for($index = 1; $index <= 7; $index ++) {
                $timestmp = $date + ($index - $week_now) * 24 * 3600;
                $all_weeks[] = [
                    'd'     => date("Y-m-d", $timestmp),
                    'z'     => $weeks[$index-1],
                    'r'     => date("m-d", $timestmp),
                    'dz'    => date("m-d", $timestmp) == date("m-d", $date)?1 : 0
                ];
            }

            $result['pre'] = date("Y-m-d", $date - ($week_now+6) * 24 * 3600);
            $result['next'] = date("Y-m-d", $date + (8-$week_now) *24 * 3600);
            $result['w'] = $all_weeks;
        }
        else {
            for($index = -2; $index <= 2; $index ++) {
                $result[2-$index] = [
                    "d" => date("Y-m-d", $date - $index * 24 * 3600),
                    "w" => $weeks[($week_now - $index + 6)%7]
                ];
            }
        }

        return $result;
    }

    /**
     * 获取解读页图表数据
     * @param idx_id 解读的dataname_id
     * @param st 开始时间
     * @param et 结束时间
     * @param num 条数（如果没有给st和et，就默认给当前时间之前的num条）
     * @return array
     */
    public function getjiedu(Request $request) {
        $id = $request->input('idx_id');

        $start_time = $request->input('st');
        $end_time = $request->input('et');
        $num = $request->input('num', 10);

        if(!is_null($id)) {
            if(!is_null($start_time) and !is_null($end_time)) {
                $all_data = EconomicCalendar::where("dataname_id", $id)
                    ->where('publish_time', '<=', $end_time)
                    ->where('publish_time', '>=', $start_time)
                    ->orderBy('publish_time', 'desc')
                    ->get();

                $next_data = EconomicCalendar::where("dataname_id", $id)
                    ->where('publish_time', '>', $end_time)
                    ->orderBy('publish_time', 'asc')
                    ->take(1)
                    ->get();
            }
            elseif(!is_null($num)) {
                $all_data = EconomicCalendar::where("dataname_id", $id)
                    ->where('publish_time', '<=', date('Y-m-d H:i:s'))
                    ->orderBy('publish_time', 'desc')
                    ->take($num-1)
                    ->get();

                $next_data = EconomicCalendar::where("dataname_id", $id)
                    ->where('publish_time', '>', date('Y-m-d H:i:s'))
                    ->orderBy('publish_time', 'asc')
                    ->take(1)
                    ->get();
            }

            $x_data = [];
            $y_data = [];

            foreach($all_data as $val) {
                $x_data[] = substr($val['publish_time'], 0, 10);
                $y_data[] = $val['published_value'];
            }

            $result = [
                "riliData"  =>  $this->dataToData(array_merge($next_data->toArray(), $all_data->toArray())),
                "xdata"     =>  $x_data,
                "ydata"     =>  $y_data
            ];

            return $result;
        }
    }

    /**
     * 获取解读页
     * @param Request $request
     * @return array
     */
    public function getjiedudata(Request $request) {
        $id = $request->input('idx_id');
        if(!is_null($id)) {
            $data = EconomicJiedu::where("dataname_id", $id)
                ->first()
                ->toArray();

            $data_info = EconomicCalendar::where('dataname_id', $id)
                ->select('dataname', 'country', 'unit')
                ->first()
                ->toArray();

            $data['dataname'] = $data_info['dataname'];
            $data['country_cn'] = $data_info['country'];
            $data['unit'] = $data_info['unit'];

            return $this->dataToData([$data]);
        }
    }

    /**
     * 数据键值转换
     * @param $data
     * @return array
     */
    private function dataToData($data){
        $key_map = [
            'id'                => 'id',
            'dataname_id'       => 'IDX_ID',
            'pub_time'          => 'stime',
            'quota_name'        => 'title',
            'country'           => 'country_cn',
            'importance'        => 'idx_relevance',
            'former_value'      => 'previous_price',
            'predicted_value'   => 'surver_price',
            'published_value'   => 'actual_price',
            'unit'              => 'UNIT',
            'pub_frequency'     => 'UPDATE_PERIOD',
            'data_influence'    => 'PARAGHRASE',
            'pub_agent'         => 'PUBLISH_ORG',
            'data_define'       => 'PARAGHRASE',
            'count_way'         => 'PIC_INTERPRET',
            'country_cn'        => 'COUNTRY_CN',
            'dataname'          => 'IDX_DESC_CN',
            'influence'         => 'res'
        ];

        $ret = [];
        foreach($data as $d) {
            $r = [];
            foreach($d as $k=>$v) {
                if(in_array($k, array_keys($key_map))) {
                    $r[$key_map[$k]] = $v;
                    if($key_map[$k] == 'res' and !in_array($r[$key_map[$k]], ['未公布', '影响较小'])) {
                        $r[$key_map[$k]] = $r[$key_map[$k]].'金银';
                    }
                }
            }

            $ret[] = $r;
        }

        return $ret;
    }

    /**
     * 数据和事件合并
     * @param data 时间
     * @param rele 重要性，格式[*_$], 其中$对应1,2,3 为1时, 获取重要性为*的; 为2时, 获取重要性大于等于*的; 为3时, *的格式为 @,% 获取重要性介于@和%之间的
     * @param type 类型, 0时获取日历，事件，假期；1时获取日历；2时获取事件
     * @param imp 重要性，格式为"1,3,5"，获取重要性为1或者3或者5的
     * @param pub 是否为发布状态，0：未发布，1：已发布
     * @return array
     */
    public function fedata(Request $request){
        $date = $request->input('date');
        $rele = $request->input('rele');
        $type = $request->input('type', 0);
        $importance = $request->input('imp');
        $pub = $request->input('pub');

        $country = $this->getcountry($request);

        if($date) {
            $date = Date('Y-m-d', strtotime($date));
        }
        else {
            $date = Date('Y-m-d', time());
        }

        $enddate = $request->input('enddate', $date);
        $enddate = date('Y-m-d', strtotime($enddate));

        if($date == Date('Y-m-d') && !is_null($pub)){
            if($pub == 0) {
                $date = Date('Y-m-d H:i:s', time());
                $enddate = $enddate.' 23:59:59';
            }
            elseif($pub == 1) {
                $date = Date('Y-m-d 00:00:00', time());
                $enddate = Date('Y-m-d H:i:s', time());
            }
        }
        else {
            $date = $date.' 00:00:00';
            $enddate = $enddate.' 23:59:59';
        }

        $calendarData = EconomicCalendar::where('pub_time', '>=', $date)
            ->where('pub_time', '<=', $enddate);

        $eventData = EconomicEvent::where('time', '>=', $date)
            ->where('time', '<=', $enddate);

        if(!is_null($country)) {
            $calendarData = $calendarData->whereIn('country', $country);
            $eventData = $eventData->whereIn('country', $country);
        }
        if($rele){
            $releData = explode('_',$rele);
            if($releData[1] == 1){
                $calendarData = $calendarData->where('importance', $releData[0]);
                $eventData = $eventData->where('importance', $releData[0]);
            }else if($releData[1] == 2){
                $calendarData = $calendarData->where('importance', '>=', $releData[0]);
                $eventData = $eventData->where('importance', '>=', $releData[0]);
            }else if($releData[1]==3){
                $calendarData = $calendarData->whereBetween('importance', explode(',', $releData[0]));
                $eventData = $eventData->whereBetween('importance', explode(',', $releData[0]));
            }
        }
        elseif(!is_null($importance)) {
            $importance = explode(',', $importance);
            $calendarData = $calendarData->whereIn('importance', $importance);
            $eventData = $eventData->whereIn('importance', $importance);
        }

        $ret = [];
        if($type == 1 or $type == 0) {
            $calendarData = $calendarData
                ->select('quota_name as title', 'pub_time as stime', 'country as country_cn', 'importance as idx_relevance', 'former_value as previous_price', 'predicted_value as surver_price', 'published_value as actual_price', 'influence as affect')
                ->orderBy('pub_time', 'asc')
                ->get()
                ->toArray();

            $calendarData = array_map(function($dt){
                $dt['type'] = 1;
                if(!in_array($dt['affect'], ['未公布', '影响较小'])) {
                    $dt['affect'] .= '金银';
                }

                return $dt;
            }, $calendarData);

            if(!empty($calendarData)) {
                $ret = array_merge($ret, $calendarData);
            }
        }

        if($type == 2 or $type == 0) {
            $eventData = $eventData->select('country', 'city as area', 'event as event_desc', 'time as event_time', 'importance')
                ->orderBy('time', 'asc')
                ->get()
                ->toArray();

            $eventData = array_map(function($dt){
                $dt['type'] = 2;
                return $dt;
            }, $eventData);

            if(!empty($eventData)) {
                $ret = array_merge($ret, $eventData);
            }
        }

        return['success'=>1, 'value'=>$ret];
    }

    /**
     * 获取参数中的日期
     * @param Request $request
     * @return array
     */
    private function getDateInparams(Request $request) {
        $date = $request->input("d", date("Y-m-d"));
        $date = substr($date, 0, 10);

        return [
            'st'=> $date." 00:00:00",
            'et'=> $date." 23:59:59"
        ];
    }
}
