<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Feedback extends Model
{

    protected $table = 'feedback';

    protected $fillable = ['content', 'phone', 'is_handling', 'ip'];

    /** 分页查询意见反馈列表
     * @param int $page 页码
     * @param int $pageSize 每页数量
     * @param string $order 排序方式
     * @param bool $isDesc 是否正序 倒叙
     * @return array
     */
    public function lists($page = 0, $pageSize = 20, $state = null, $order = 'id', $isDesc = true)
    {
        $qTable = ' jujin8_feedback';

        $where = '  ';
        if ($state == '0' || $state == '1') {
            $where = " where is_handling = $state ";
        }

        $orderBy = ' ORDER BY ' . $order . ' ' . ($isDesc ? 'DESC' : 'ASC');
        $start = $page * $pageSize;
        $limit = ' limit ' . $start . ' , ' . $pageSize;

        $sql = "select * from $qTable $where $orderBy $limit";
        $ret = DB::connection()->select($sql);

        $countSql = 'select count(id) AS count from ' . $qTable . $where;

        $count = DB::connection()->select($countSql);

        return [
            "list" => $ret,
            'count' => $count[0]->count,
            'page' => $page,
            'pageSize' => $pageSize
        ];
    }


}