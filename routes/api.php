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
Route::get('accountLog', "AccountController@getAccountLog");
Route::get('removeAccountType', "AccountController@removeAccountType");
Route::get('removeAccount', "AccountController@removeAccount");
Route::get('removeAccountLog', "AccountController@removeAccountLog");
Route::post('addAccountType', "AccountController@addOrUpdateAccountType");
Route::post('addAccount', "AccountController@addOrUpdateAccount");
Route::post('addAccountLog', "AccountController@addOrUpdateAccountLog");
Route::get('getSentence', "SentenceController@index");
Route::post('addWxUser', "WxController@addOrUpdateWxUser");
Route::get('getUserInfo', 'WxController@getUserInfo');
Route::get('getWxOpenid', 'WxController@getWxOpenid');
