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

Route::get('/', function () {return view('welcome');});

Route::group(['prefix'=>'admin','middleware'=>'check.login'],function (){
    Route::post('ronglanfb/projectlist','Admin\ProjectController@projectlist'); //项目列表
    Route::post('ronglanfb/projectshenhe','Admin\ProjectController@projectshenhe');//审核

    Route::post('ronglanfb/Certificate','Admin\UserController@Certificate'); //认证列表
    Route::post('ronglanfb/shenhe','Admin\UserController@shenhe');//审核

    Route::post('ronglanfb/clientlist','Admin\UserController@clientlist');//专属客服
    Route::post('ronglanfb/clientupdate','Admin\UserController@clientupdate');//联系结果

    Route::post('ronglanfb/userlist','Admin\UserController@userlist');//认证过的人员名单
    Route::post('ronglanfb/projectsend','Admin\ProjectController@projectsend');//项目推送

});


