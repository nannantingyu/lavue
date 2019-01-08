<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function Sodium\add;

class ArticleController extends Controller
{

    public function moreList(Request $request)
    {
        $size = $request->input('size');
        if (is_null($size) and !\numcheck::is_int($size)) {
            $size = 10;
        }

        $typeId = $request->input('typeId');
        if (is_null($typeId) and !\numcheck::is_int($typeId)) {
            $typeId = null;
        }

        $time = $request->input('time');
        $where = "";
        $sql = '';
        if (is_null($typeId)) {
            $sql = 'SELECT a.id,title,publish_time AS created,keywords AS catTitle,description AS metadesc,image AS imgurl,author AS author,source_site AS sourceSite,source_url AS sourceUrl FROM jujin8_article a INNER JOIN jujin8_article_body as body ON a.id = body.aid ';
            $ids = $this->getFilterIds();
            $where = " WHERE aid not in ($ids) ";
        } else {
            $where = " where cid = $typeId ";
            $sql = "SELECT a.id,title,publish_time AS created,keywords AS catTitle,description AS metadesc,image AS imgurl,author AS author,source_site AS sourceSite,source_url AS sourceUrl FROM jujin8_article a LEFT JOIN jujin8_article_category ac ON a.id=aid  INNER JOIN jujin8_article_body as body ON a.id = body.aid ";
        }

        if (!is_null($time)) {
            $where = "$where and publish_time < '$time' AND state = 1";
        }

        $sql = "$sql $where ORDER BY publish_time DESC LIMIT $size";

        $lists = DB::select($sql);

        for ($i = 0; $i < count($lists); $i++) {
            $lists[$i]->imgurl = str_replace('uploads/crawler', 'uploads', $lists[$i]->imgurl);//a或b或c都替换成a
        }
        return ['success' => 1, 'data' => $lists];
    }

    private function getFilterIds()
    {
        $idsStr = $this->getFilter();
        $sql = "SELECT aid as id FROM jujin8_article_category where cid in (SELECT id FROM jujin8_category WHERE ename in ($idsStr))";

        $list = DB::select($sql);

        $ids = '';
        for ($i = 0; $i < count($list); $i++) {
            if ($i != 0) {
                $ids = $ids . ',';
            }
            $ids = $ids . $list[$i]->id;
        }
        return $ids;
    }

    private function getFilter()
    {
        $filterName = DB::selectOne("SELECT `value` FROM jujin8_config WHERE `key`='articleFilter' LIMIT 1")->value;
        $filterName = explode(',', $filterName);
        $idsStr = "";
        for ($i = 0; $i < count($filterName); $i++) {
            if ($i != 0) {
                $idsStr = "$idsStr,";
            }
            $idsStr = "$idsStr '$filterName[$i]'";
        }
        return $idsStr;
    }

}
