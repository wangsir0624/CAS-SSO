<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'tgc_keepalive'], function() {
    Route::get('/', 'IndexController@getIndex');
    Route::get('login', 'AuthController@getLogin');
    Route::get('dingding/login', 'AuthController@getDingdingLogin');
    Route::get('logout', 'AuthController@getLogout');
});

Route::get('api/userInfo', 'ApiController@getUserInfo');