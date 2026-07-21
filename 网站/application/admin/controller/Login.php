<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;
class Login extends Controller
{
	//检查是否登录
    public function _initialize()
    {
        $request = \think\Request::instance();
        $contrname = $request->controller();
        $actionname = $request->action();
        $this->assign('contrname',$contrname);
        $this->assign('actionname',$actionname);
        if(session('adminid')){
            $this->error('您已登录',"/admin/index");
        }
    }
    public function index()
    {
    	return $this->fetch();
    }
    public function ajaxlogin()
    {
        $username=remove_xss(input('post.username'));
        $password=dsp_password(remove_xss(input('post.password')));
        
        $re=Db::name("admin")->where('username',$username)->find();
        if (empty($re)) {
            $this->error('账户不存在');
        }
        if ($password!=$re['password']) {
            $this->error('密码不正确，可前往71jc.cn获取密码');
        }else{
            Session::set('adminid',$re['id']);
            Session::set('adminaccount',$re['username']);
            Session::set('adminname',$re['nickname']);
            Session::set('adminqq',$re['qq']);
            $this->success('登录成功','/admin/index');
        }
    }
}