<?php

use App\Http\Middleware\CheckAge;

Route::get('/', "IndexController@index");
Route::get('/home/{age}', function() {
    return "这个是我写的页面哦";
})->middleware(CheckAge::class);

Route::get('/forbidden', function() {
    return "您的年龄太小，不能访问哦";
});

//Index
Route::get('/blog_{id}', "IndexController@detail");
Route::get('/watch_{id}', "IndexController@detail");
Route::get('/incre_{id}', "AjaxController@incre");
Route::get('/hotkey', "IndexController@hotkey");
Route::get('/weibo', "IndexController@weibo");
Route::get('/keywords', "IndexController@keywords");
Route::get('/hotsearch', "IndexController@hotsearch");

//Ajax
Route::get('/baidu_tuisong', "AjaxController@baidu_tuisong");
Route::get('/body_src_repl', "AjaxController@body_src_repl");
Route::get('/image', "ApiController@img");
Route::get('/kx', "ApiController@getkx");
Route::get('/hotweibo', "ApiController@hotweibo");

//Login
Route::get('/login', "LoginController@index");
Route::get('/register', "LoginController@register");
Route::get('/logout', "LoginController@logout");
Route::get('/check/{check_str}', "LoginController@check");
Route::post('/login', "LoginController@login_post");
Route::post('/register', "LoginController@register_post");
Route::any('/captcha', function()
{
    return captcha();
});

//List
Route::get('/list_{type}', "ListController@lists");
Route::get('/search_{keywords}_{page}', "ListController@search");
Route::get('/type_{keywords}_{page}', "ListController@type");
Route::get('/baidu_{id}', "ListController@baidusearch");
Route::get('/weibo_{id}', "ListController@weibosearch");
Route::get('/keys_{key}_{page}', "ListController@keys");
Route::get('/author_{author}', "ListController@author");
Route::get('/keys', "ListController@keywords");
Route::get('/news', "ListController@news");
Route::get('/hots', "ListController@hots");

//Caijing
$router->get('/getDates', 'EconomicController@getDates');
$router->get('/getPastorWillFd', 'EconomicController@getPastorWillFd');
$router->get('/getWeekData', 'EconomicController@getWeekData');
$router->get('/getjiedu', 'EconomicController@getjiedu');
$router->get('/getjiedudata', 'EconomicController@getjiedudata');
$router->get('/getcjdatas', 'EconomicController@getcjdatas');
$router->get('/getcjevent', 'EconomicController@getcjevent');
$router->get('/getcjholiday', 'EconomicController@getcjholiday');
$router->get('/fedata', 'EconomicController@fedata');
$router->get('/rili', 'EconomicController@rili');

//kuaixun
$router->get('/kuaixun', 'KuaixunController@kuaixun');
$router->get('/kuaixun_{id}', 'KuaixunController@kuaixun_detail');

//hq
$router->get('/hq_btc', 'HqController@index');

//other
$router->get('/sitemap.xml', 'ToolController@sitemap');
$router->get('/xmlsitemap/article{page}.xml', 'ToolController@site');
$router->post('/trans', 'ToolController@trans_downfile');

Route::get('/test', "Test2Controller@index");

//candy
$router->get('/residential', 'HouseController@residential');
$router->get('/house', 'HouseController@history');
$router->get('/areahouse', 'HouseController@getAreaResidential');
$router->get('/crawl/{name}', 'HouseController@crawl');