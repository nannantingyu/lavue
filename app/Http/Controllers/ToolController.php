<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EconomicCalendar;
use App\Models\EconomicJiedu;
use App\Models\EconomicEvent;
use App\Models\EconomicHoliday;

class ToolController extends Controller
{
    public function __construct() {
        $this->pagecount = 1000;
        $this->sitePath = "../public/xmlsitemap/sitemap.xml";
    }

    public function sitemap(Request $request) {
        $sitemap = simplexml_load_file($this->sitePath);
        $xmlstring = '<?xml version="1.0" encoding="utf-8"?><sitemapindex></sitemapindex>';
        $newsitemap = simplexml_load_string($xmlstring);

        //规则：
        //1.将文章的放到一个名称的sitemap: aritle1.xml，每pagecount一个xml文件，超过新建一个文件article2.xml
        //2.强词条单独放在一个文件entry.xml
        //2.强Tag页单独放在一个文件tag.xml
        //4.目录放在mulu.xml中，手动维护
        //5.自动维护的sitemap，每次读取时，读取上一次生成的url地址的最新时间start，本次只将新的url地址(时间从start-当前)的添加到新的中

        /**index只负责生成、更新sitemap索引文件**/
        //判断文章总共的条数，每pagecount条生成一个地址
        $countArticle = DB::table("weixin_article")->where("state", 0)->count();
        $page = ceil($countArticle / $this->pagecount);

        $maxPage = 1;
        foreach($sitemap as $key=>$val){
            $children = $val->children();
            if(preg_match('/\/article(\d+)/', $children->loc, $match)){
                $maxPage = $match[1] > $maxPage?$match[1]:$maxPage;
            }
        }

        //添加新增加的sitemap
        for($index = 1; $index <= $page; $index ++){
            $has = false;
            foreach($sitemap as $key=>$val){
                $children = $val->children();
                if($children->loc == 'https://www.yjshare.cn/xmlsitemap/article'.$index.'.xml'){
                    $has = true;
                    break;
                }
            }

            if(!$has){
                $newsite = $newsitemap->addChild('sitemap');
                $newsite->addChild('loc', 'https://www.yjshare.cn/xmlsitemap/article'.$index.'.xml');
                $newsite->addChild('lastmod', date('Y-m-d'));
            }
            else if($maxPage >= $page && $children->loc == 'https://www.yjshare.cn/xmlsitemap/article'.$maxPage.'.xml')
            {
                $children->lastmod = date('Y-m-d');
            }
        }
        //添加老的sitemap
        foreach($sitemap as $key=>$val){

            if(!strstr($val->loc, 'article')){
                $val->lastmod = date('Y-m-d');
            }

            $site = $newsitemap->addChild($key);
            foreach($val->children() as $k=>$v){
                $site->addChild($k, $v);
            }
        }

        if(file_exists($this->sitePath)){
            unlink($this->sitePath);
        }

        $newsitemap->saveXML($this->sitePath);
        print_r($newsitemap->asXML());
    }

    public function site(Request $request, $page) {
        $xmlstring = '<?xml version="1.0" encoding="utf-8"?><urlset></urlset>';
        $newsite = simplexml_load_string($xmlstring);

        $otherMap = array();    //其他缓存sitemap
        $articleMap = array();  //文章sitemap


        $xml_path = dirname($this->sitePath);

        //读取缓存的sitemap
        $handle = opendir($xml_path);
        while($file = readdir($handle)){
            if($file != '.' && $file != '..'){
                if(strpos($file, 'article') === 0){
                    $articleMap[] = $file;
                }
                else{
                    $otherMap[] = $file;
                }
            }
        }

        $page = is_null($page)?1:$page;
        if(!empty($articleMap)){
            rsort($articleMap);
            preg_match('/article(\d+)/', $articleMap[0], $match);
        }

        $contentCounts = DB::table("weixin_article")->where("state", 0)->count();

        $allPage = ceil($contentCounts / $this->pagecount);

        if($page > $allPage){
            $page = $allPage;
        }

        //如果第一次读，没有文件，须创建
        $maxSite = $xml_path.'/article'.$page.'.xml';
        $start = null;

        $site = null;
        if(file_exists($maxSite)){
            $site = simplexml_load_file($maxSite);
            $latest = $site->children()[0]->loc;
            $latest = explode('/', $latest);
            $latest = explode('_', $latest[count($latest) - 1]);
            $latest = $latest[1];

            $start = DB::table("weixin_article")->where("id", $latest)->pluck("created_time");
        }

        $content = DB::table("weixin_article")
            ->where("state", 0);
        if($start) {
            $content = $content->where("created_time", ">=", $start);
        }

        $content = $content->orderBy('created_time', 'desc')
            ->skip(($page-1) * $this->pagecount)
            ->take($this->pagecount)
            ->select('id', 'created_time')
            ->get();

        foreach($content as $key=>$val){
            $url = 'https://www.yjshare.cn/blog_'.$val->id;
            $sitepath = $newsite->addChild('url');
            $sitepath->addChild('loc', $url);
            $sitepath->addChild('priority', 0.8);
            $sitepath->addChild('lastmod', date('Y-m-d', strtotime($val->created_time)));
            $sitepath->addChild('changefreq', 'daily');
        }

        //添加以前的
        if($site){
            foreach($site as $key=>$val){
                $sitepath = $newsite->addChild('url');
                $sitepath->addChild('loc', $val->loc);
                $sitepath->addChild('priority', $val->priority);
                $sitepath->addChild('lastmod', $val->lastmod);
                $sitepath->addChild('changefreq', 'daily');
            }
        }

        if(file_exists($maxSite)){
            unlink($maxSite);
        }

        $newsite->saveXML($maxSite);
        print_r($newsite->asXML());
    }
}
