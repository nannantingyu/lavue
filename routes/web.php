<?php

use App\Http\Middleware\CheckAge;

Route::get('/', "IndexController@index");
Route::get('/home/{age}', function() {
    return "这个是我写的页面哦";
})->middleware(CheckAge::class);

Route::get('/forbidden', function() {
    return "您的年龄太小，不能访问哦";
});
Route::get('/blog_{id}', "IndexController@detail");
Route::get('/watch_{id}', "IndexController@detail");
Route::get('/incre_{id}', "AjaxController@incre");
Route::get('/search_{keywords}_{page}', "IndexController@search");
Route::get('/type_{keywords}_{page}', "IndexController@type");
Route::get('/baidu_tuisong', "AjaxController@baidu_tuisong");
Route::get('/body_src_repl', "AjaxController@body_src_repl");
Route::get('/list_{type}', "ListController@lists");
Route::get('/login', "LoginController@index");
Route::get('/register', "LoginController@register");
Route::get('/logout', "LoginController@logout");

Route::get('/baidu_{id}', "ListController@baidusearch");
Route::get('/weibo_{id}', "ListController@weibosearch");

Route::post('/login', "LoginController@login_post");
Route::post('/register', "LoginController@register_post");

Route::any('/captcha', function()
{
    return captcha();
});

Route::get('/test', "TestController@index");
Route::get('/check/{check_str}', "LoginController@check");

Route::get('/image', "IndexController@img");
Route::get('/hotkey', "IndexController@hotkey");
Route::get('/weibo', "IndexController@weibo");
Route::get('/keywords', "IndexController@keywords");
Route::get('/keys_{key}_{page}', "ListController@keys");
Route::get('/kx', "KuaixunController@getkx");

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