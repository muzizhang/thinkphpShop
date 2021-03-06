<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------


//   后台
//   快捷路由
Route::rule('/','index/Login/index');
Route::rule('login/Login$','index/Login/Login')->middleware('InLoginCheck');
Route::rule('login/Verify','index/Login/Verify');
Route::rule('login/logout','index/Login/logout');



Route::group('',function(){
    Route::get('/index','index/Index/index');
    Route::get('/home','index/Index/home');
    Route::controller('product','index/Product');
    Route::controller('article','index/Article');
    Route::controller('info','index/Info');
    Route::controller('manage','index/Manage');
    Route::controller('order','index/Order');
    Route::controller('pay','index/Pay');
    Route::controller('system','index/System');
    Route::controller('user','index/User');
    Route::controller('manage','index/Manage');
    Route::controller('base','index/Base');
})->middleware('app\http\middleware\LoginCheck');


//  前台
Route::controller('homeRegister','home/Register');
Route::controller('homeLogin','home/Login');
Route::controller('homeIndex','home/Index');
Route::controller('homeCart','home/Cart');
Route::controller('homeGood','home/Good');
Route::controller('homeSeckill','home/Seckill');
Route::controller('homeUser','home/User');

Route::controller('homePay','home/PayController');
//  测试
//  资源路由
// Route::resource('test','home/test');
// Route::rule('test/read','index/test/read');
// Route::rule('test/redis','index/test/redis');
// Route::rule('test/ip','index/test/ip');
return [

];
