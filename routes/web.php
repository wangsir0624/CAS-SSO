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
    Route::get('dingding/login', 'AuthController@getDingdingLogin');
    Route::get('logout', 'AuthController@getLogout');
});

Route::get('api/userInfo', 'ApiController@getUserInfo');

Route::get('test', function(\Wangjian\Dingding\DingdingClient $client, \Illuminate\Http\Request $request) {
   $userId = 1;
   $type = 'dingding';

   $user = App\Entity\User\User::find('13922462837', 'mobile', 'dingding');
   var_dump($user->toJson());
});