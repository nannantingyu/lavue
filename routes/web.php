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
Route::get('/baidu_tuisong', "AjaxController@baidu_tuisong");
Route::get('/body_src_repl', "AjaxController@body_src_repl");