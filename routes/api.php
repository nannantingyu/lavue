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

Route::get('accountType', "AccountController@getAccountType");
Route::get('account', "AccountController@getAccount");
Route::get('favoraccount', "AccountController@getFavorAccount");
Route::get('accountLog', "AccountController@getAccountLog");
Route::post('removeAccountType', "AccountController@removeAccountType");
Route::post('removeAccount', "AccountController@removeAccount");
Route::post('removeAccountLog', "AccountController@removeAccountLog");
Route::post('addAccountType', "AccountController@addOrUpdateAccountType");
Route::post('addAccount', "AccountController@addOrUpdateAccount");
Route::post('favorAccount', "AccountController@favorAccount");
Route::post('addAccountLog', "AccountController@addOrUpdateAccountLog");
Route::get('getMonthAll', 'AccountController@getMonthAll');

Route::get('accountData', "AccountController@accountData");
Route::get('getSentence', "SentenceController@index");
Route::post('addWxUser', "WxController@addOrUpdateWxUser");
Route::get('getUserInfo', 'WxController@getUserInfo');
Route::get('getWxOpenid', 'WxController@getWxOpenid');

Route::get('getThisYear', 'AccountController@getThisYear');