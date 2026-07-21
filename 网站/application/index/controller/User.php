<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;
use think\request;
class User extends Controller
{
    //检查是否登录
    public function _initialize()
    {
        $this->getingfo();
        $request = \think\Request::instance();
        $contrname = $request->controller();
        $actionname = $request->action();
        $this->assign('contrname',$contrname);
        $this->assign('actionname',$actionname);
        if(!session('userid')&&!session('mail')){
            $this->error('请先登录！',"/login.html");
        }
    }
    
    public function index()
    {
        $userid=session('userid');
        $userinfo=Db::name('account')->where('id',$userid)->find();
        $todayStart= strtotime(date('Y-m-d 00:00:00', time())); //当天时间
        $todayEnd= strtotime(date('Y-m-d 23:59:59', time())); //当天结束时间
        //判断会员是否到期
        
        if (!empty($userinfo['vip_endtime'])) {
            //到期时间<当前时间
            $loginlog=Db::name('loginlog')->where('login_userid',$userid)->where('login_time','gt',$todayStart)->where('login_time','lt',$todayEnd)->count();
            if ($loginlog==1) {
                if ((int)$userinfo['vip_endtime']<time()) {
                    //修改成到期并把次数还原
                    $uservipcountmodel=Db::name('config')->where('dsp_name', 'user_count')->value('dsp_value');
                    $vip_data=[
                        'is_vip'    =>1,
                        'vip_type'  =>0,
                        'day_count'   =>$uservipcountmodel,
                    ];
                    $upvip=Db::name('account')->where('id',$userid)->update($vip_data);
                    $login_log=[
                        'login_userid'  => session('userid'),
                        'login_time'    =>strtotime(date('y:m:d')),
                        'login_ip'  =>GetIP(),
                    ];
                    $loginlog=Db::name('loginlog')->insert($login_log);
                }
            }
        }
        
        //查询开通会员日志
        $viplog=Db::name('viplog')->where('log_userid',$userid)->order("id desc")->limit(5)->select();
        if (empty($viplog)) {
            $viplog='0';
            $this->assign('viplog',$viplog);
        }
        //查询接口
        $apilist=Db::name('interface')->where('api_sta',1)->select();
        $this->assign('apilist',$apilist);
        $this->assign('viplog',$viplog);
        //$this->assign('userinfo',$userinfo);
        //$this->success("退出成功","/login.html");
        $userinfo=Db::name('account')->where('id',$userid)->find();
        
        $loginlog=Db::name('loginlog')->where('login_userid',$userid)->where('login_time','gt',$todayStart)->where('login_time','lt',$todayEnd)->count();

        if ($loginlog==1) {
            if ($userinfo['vip_type']==0) {
                $vtype='user_count';
            }elseif ($userinfo['vip_type']==1) {
                $vtype='user_vip1';
            }elseif ($userinfo['vip_type']==2) {
                $vtype='user_vip2';
            }
            elseif ($userinfo['vip_type']==3) {
                $vtype='user_vip3';
            }
            $uservipcountmodel=Db::name('config')->where('dsp_name', $vtype)->value('dsp_value');
            $usercount['day_count']=$uservipcountmodel;
            $upusercount=Db::name('account')->where('id',$userid)->update($usercount);
            $upusercount=Db::name('account')->where('id',$userid)->update($usercount);
            $login_log=[
                'login_userid'  => session('userid'),
                'login_time'    =>strtotime(date('y:m:d')),
                'login_ip'  =>GetIP(),
            ];
            $loginlog=Db::name('loginlog')->insert($login_log);
            $userinfo=Db::name('account')->where('id',$userid)->find();
            $this->assign('userinfo',$userinfo);
            $this->getingfo();
            return $this->fetch();
        }else{
            $userinfo=Db::name('account')->where('id',$userid)->find();
            $this->assign('userinfo',$userinfo);
            $this->getingfo();
            return $this->fetch();
        }
    }
    public function vip()
    {
    	$this->getingfo();
    	$viplist=Db::name('viptype')->limit(3)->select();
    	$this->assign('viplist',$viplist);
        //获取支付
        $payt=Db::name('paytype')->where('is_use',1)->select();
        $this->assign('payt',$payt);
        return $this->fetch();
    }
    //获取网站基本信息
    function getingfo()
    {
        $info=Db::name('info')->where('id',1)->find();
    	$this->assign('info',$info);
    }
    public function loginout()
    {
        $this->getingfo();
        session('userid',null);
        session('mail',null);
        
        $this->success('退出成功',"/login.html");
    }
    public function getapi()
    {
        $userid=session('userid');
        $uinfo=Db::name('account')->where('id',$userid)->find();
        $this->assign('uinfo',$uinfo);
        $this->getingfo();
        return $this->fetch();
    }
    public function settoken()
    {
        $userid=session('userid');
        //查询接口权限开发
        $userapigetmodel=Db::name('config')->where('dsp_name', 'vip_get')->value('dsp_value');
        //查询是否是会员
        $uinfo=Db::name('account')->where('id',$userid)->find();
        $is_vip=$uinfo['is_vip'];
        $vip_type=$uinfo['vip_type'];
        if (!empty($uinfo['api_token'])) {
            $this->error("无需重复申请");
        }
        if ($userapigetmodel==0) {
            //关闭会员用户申请，普通用户申请
            $token=md5($_SERVER['SERVER_NAME'].$userid);
            $token=endecodeUserId($token);
            $data=[
                'api_token'=>$token,
                'is_api'=>0
            ];
            $re=Db::name('account')->where('id',$userid)->update($data);
            if (!empty($re)) {
                $this->success("申请成功！");
            }else{
                $this->error("申请失败！");
            }
        }
        if ($userapigetmodel==1) {
            //查询当前 可开通的等级
            $uservipktmodel=Db::name('config')->where('dsp_name', 'vip_int')->value('dsp_value');
            if ($is_vip==1) {
                $this->error("请开通会员后再申请");
            }
            //1   2
            if ($vip_type<$uservipktmodel) {
                $this->error("会员等级不足！");
            }
            $token=md5($_SERVER['SERVER_NAME'].$userid);
            $token=endecodeUserId($token);
            $data=[
                'api_token'=>$token,
                'is_api'=>0
            ];
            $re=Db::name('account')->where('id',$userid)->update($data);
            if (!empty($re)) {
                $this->success("申请成功！");
            }else{
                $this->error("申请失败！");
            }
        }
    }
    public function open()
    {
        $this->getingfo();
        return $this->fetch();
    }
    public function setting()
    {
        $userid=session('userid');
        $userinfo=Db::name('account')->where('id',$userid)->find();
        $this->assign('userinfo',$userinfo);
        $this->getingfo();
        return $this->fetch();
    }
    public function ajaxupuserpwd()
    {
        if (!$this->request->isPost()) {
            $this->error("参数错误");
        }else{
            $userid=session('userid');
            $userinfo=Db::name('account')->where('id',$userid)->find();
            $oldpass=remove_xss(input('post.old'));
            $oldpass=dsp_password($oldpass,$authCode='');
            $newpass=remove_xss(input('post.newpass'));
            $newpass=dsp_password($newpass,$authCode='');
            if ($oldpass!=$userinfo['email_password']) {
                $this->erroe("原密码输入不正确");
            }else{
                $u_pass['email_password']=$newpass;
                $re=Db::name('account')->where('id',$userid)->update($u_pass);
                if (empty($re)) {
                    $this->error("修改失败");
                }else{
                    $this->success('修改成功,请重新登录','/loginout.html');
                }
            }
        }
    }
}

