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


Route::post('ronglanfb/register','Api\LoginController@register');//注册
Route::get('ronglanfb/getcode','Api\LoginController@getcode');//注册
Route::get('ronglanfb/getactoken','Api\LoginController@getactoken');//注册
Route::post('ronglanfb/sendregistercode','Api\LoginController@sendregistercode');//发送注册验证码
Route::post('ronglanfb/sendlogincode','Api\LoginController@sendlogincode');//发送注册验证码
Route::post('ronglanfb/login','Api\LoginController@login');//登录
Route::post('ronglanfb/mobilelogin','Api\LoginController@mobilelogin');//手机验证登录
Route::post('ronglanfb/emailcode','Api\MailController@emailcode');//发送邮箱验证码

Route::group(['prefix'=>'ronglanfb','middleware'=>'check.login'],function (){
    Route::get('userlist','Api\UserController@userlist');
    Route::post('updatepassword','Api\UserController@updatepassword'); //修改密码
    Route::post('headuploads','Api\UserController@headuploads');//上传头像
    Route::post('updatehead','Api\UserController@updatehead');//修改头像
    Route::post('updateemail','Api\UserController@updateemail');//修改邮箱
    Route::post('updatepay','Api\UserController@updatepay');//修改支付宝账号
    Route::post('fileuploads','Api\UserController@fileuploads');//上传文件

    Route::post('PersonalCertificate','Api\UserController@PersonalCertificate');//个人认证
    Route::post('ProgrammerCertificate','Api\UserController@ProgrammerCertificate');//程序员认证
    Route::post('Enterprisecertificate','Api\UserController@Enterprisecertificate');//企业认证

    Route::post('mywork','Api\UserController@mywork');//我的工作
    Route::post('myhomepage','Api\UserController@myhomepage');//我的主页

    Route::post('projectplanning','Api\ProjectController@projectplanning');//项目规划
    Route::post('projectwhole','Api\ProjectController@projectwhole');//整包项目
    Route::post('projectoutsourcing','Api\ProjectController@projectoutsourcing');//外包项目
    Route::post('addproject','Api\ProjectController@addproject');//新建项目

});
