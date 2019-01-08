<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/jiedu', 'Api\CalendarController@getjiedu');
Route::get('/kx', 'Api\KuaixunController@getkx');
Route::get('/bkx', 'Api\KuaixunController@getBlockKx');
Route::get('/ukx', 'Api\KuaixunController@getUnionKx');
Route::get('article/more-list', 'Api\ArticleController@moreList');

//意见反馈
Route::post('/addFeedback', 'Api\FeedbackController@addFeedback');
Route::get('feedback/getList', 'Api\FeedbackController@getList');
Route::post('feedback/setStates', 'Api\FeedbackController@setState');


//快讯
Route::post('kuaixun/addKuaiXun', 'KuaiXunController@addKuaiXun');
Route::get('kuaixun/getList', 'KuaiXunController@getList');
Route::get('kuaixun/pagelist', 'KuaiXunController@getPageList');
//菜单
Route::post('menu/add', 'MenuController@add');
Route::get('menu/list', 'MenuController@getList');
//投资工具
Route::post('tool/add', 'ToolController@add');
Route::get('tool/list', 'ToolController@getList');

//财经日历
Route::post('calendar/add', 'CalendarController@add');
Route::get('calendar/list', 'CalendarController@getList');
//财经事件
Route::post('event/add', 'CalendarEventController@add');
Route::get('event/list', 'CalendarEventController@getList');
//财经假期
Route::post('holiday/add', 'CalendarHolidayController@add');
Route::get('holiday/list', 'CalendarHolidayController@getList');
//Banner活动
Route::post('banner/add', 'BannerController@add');
Route::post('banner/setState', 'BannerController@setState');
Route::get('banner/list', 'BannerController@getList');
//Banner活动
Route::post('banner/addCategory', 'BannerCategoryController@add');
Route::post('banner/setCategoryState', 'BannerCategoryController@setState');
Route::get('banner/listCategory', 'BannerCategoryController@getList');


Route::get('article/listBySite', 'ArticleController@listBySite');
Route::post('article/setStates', 'ArticleController@setState');

Route::post('user/sms', 'Api\LoginController@getSms');
Route::post('user/register', 'Api\LoginController@register');
Route::post('user/login', 'Api\LoginController@login');
Route::post('user/reset', 'Api\LoginController@forgotPwd');
Route::post('user/validator', 'Api\LoginController@validatorAction');
Route::get('user/loginStatus', 'Api\LoginController@loginStatus');
Route::get('user/logout', 'Api\LoginController@logout');

Route::get('user/captcha', 'Api\LoginController@getCaptcha');
Route::get('test', 'TestController@index');
Route::get('test2', 'TestController@index2');