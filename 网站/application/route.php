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
use think\Route;

//首页模块
Route::rule('index','index/Index/index');//首页路由
//API模块
Route::rule('api/index','index/Api/index');//用户APi
Route::rule('jiexi','index/Api/jiexi');//API
Route::rule('open','index/User/open');//开发文档
Route::rule('api/dsp/:token/:userid/','index/Api/dsp');//短视频api
//登录模块
Route::rule('login','index/Login/index');//用户登录路由地址
Route::rule('ajaxlogin','index/Login/ajaxlogin');//用户ajax登录路由地址
Route::rule('register','index/Login/register');//用户ajax注册路由地址
Route::rule('ajaxregister','index/Login/ajaxregister');//用户提交注册路由地址
Route::rule('valRegister','index/login/valRegister');//开启邮箱验证模块后的验证地址
//用户模块
Route::rule('user','index/User/index');//用户中心
Route::rule('vip','index/User/vip');//用户开通VIP路由地址
Route::rule('getapi','index/User/getapi');//用户申请接口路由地址
Route::rule('settoken','index/User/settoken');//ajax申请接口
Route::rule('loginout','index/User/loginout');//退出登录
Route::rule('setting','index/User/setting');//账号设置
Route::rule('ajaxupuserpwd','index/User/ajaxupuserpwd');//Ajax修改密码
//支付模块
Route::rule('pay/index','index/Pay/index');//支付
Route::rule('pay/alipayquery','index/Pay/alipayquery');//支付宝查单
Route::rule('pay/epaynotify','index/Pay/epaynotify');//易支付回调
Route::rule('pay/kmpay','index/Pay/kmpay');//卡密支付
