<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class User extends Controller
{
	//检查是否登录
    public function _initialize()
    {
        $request = \think\Request::instance();
        $contrname = $request->controller();
        $actionname = $request->action();
        $this->assign('contrname',$contrname);
        $this->assign('actionname',$actionname);
        if(!session('adminid')){
            $this->error('请先登录！',"/admin/login.html");
        }
    }
    public function index()
    {

        $userlist=Db::name('account')->where("status",1)->select();
        $this->assign('userlist',$userlist);
        return $this->fetch();
    }
    public function wuser()
    {
        $userlist=Db::name('account')->where("status",0)->select();
        $this->assign('userlist',$userlist);
        return $this->fetch();
    }
    public function setpass()
    {
        $uid=(int)input('post.id');
        $pass='123456';
        $data['email_password']=dsp_password($pass,$authCode='');
        $re=Db::name('account')->where('id',$uid)->update($data);
        if (empty($re)) {
            $this->error("重置失败");
        }else{
            $this->success("重置成功");
        }
    }
    public function deleteuser()
    {
        $uid=(int)input('post.id');
        if ($uid==0) {
            $re=Db::name('account')->where('status',0)->delete();
            $re=Db::name('account')->where('id',$uid)->delete();
            if (empty($re)) {
                $this->error("删除失败");
            }else{
                $this->success("删除成功");
            }
        }else{
            $re=Db::name('account')->where('id',$uid)->find();
            if (empty($re)) {
                $this->error("用户不存在");
            }else{
                $re=Db::name('account')->where('id',$uid)->delete();
                if (empty($re)) {
                    $this->error("删除失败");
                }else{
                    $this->success("删除成功");
                }
            }
        }
    }
    public function changeStatus()
    {
        $uid=(int)input('post.id');
        $status=(int)input('post.status');
        $re=Db::name('account')->where('id',$uid)->find();
        if (empty($re)) {
            $this->error("用户不存在");
        }else{
            $data['status']=$status;
            $re=Db::name('account')->where('id',$uid)->update($data);
            if (empty($re)) {
                $this->error("设置失败");
            }else{
                $this->success("设置成功");
            }
        }
    }
    public function edituser()
    {
        $uid=(int)input('get.id');
        $re=Db::name('account')->where('id',$uid)->find();
         if (empty($re)) {
            $this->error("用户不存在");
        }else{
            //获取VIP方式
            $viplist=Db::name('viptype')->select();
            $this->assign('viplist',$viplist);
            //判断是否是VIP
            $vip=$re['is_vip'];

            if ($vip==0) {
                $vid=$re['vip_type'];
                $vlv=Db::name('viptype')->where("id",$vid)->find();
                $this->assign('vlv',$vlv);
            }
            $this->assign('re',$re);
            return $this->fetch();
        }
    }
    public function zengsong()
    {
        $uid=(int)input('post.uid');
        $re=Db::name('account')->where('id',$uid)->find();
        $viptype=(int)input('post.viptype');
        $vde=Db::name('viptype')->where('id',$viptype)->find();
        $vipday=(int)input('post.vipday');
        $uservipcountmodel = Db::name('config')->where('dsp_name', $vde['vip_bs'])->value('dsp_value');
        
        if (empty($uid)) {
            $this->error("数据错误");
        }
        if (empty($re)) {
            $this->error("用户不存在");
        }
        if (empty($viptype)) {
            $this->error("请选择会员方式");
        }
        if (empty($vipday)) {
            $this->error("请输入赠送时长");
        }
        //先去查询用户是否会员
        if ($re['is_vip']==0) {
            # code...
            //是会员 还没有到期 
            //拿到到期时间
            $data['vip_type']=$viptype;
            $data['vip_endtime']=$re['vip_endtime']+$vipday*24*60*60;
            $data['day_count']=$uservipcountmodel;
            $res=Db::name('account')->where("id",$uid)->update($data);
            if (!empty($res)) {
                $log_data=[
                    'log_userid'=>$uid,
                    'log_orderid'=>'',
                    'log_mail'=>'',
                    'log_text'=>'系统赠送VIP'.$vde['vip_name'].",".$vipday."天",
                    'log_time'=>time(),
                ];
               
                $add_log=Db::name('viplog')->insert($log_data);
                $this->success("赠送成功");
            }else{
                $this->error("赠送失败");
            }
        }else if($re['is_vip']==1){
            $data['is_vip']=0;
            $data['vip_type']=$viptype;
            $data['vip_begintime']=time();
            $data['day_count']=$uservipcountmodel;
            $data['vip_endtime']=time()+$vipday*24*60*60;
            $res=Db::name('account')->where("id",$uid)->update($data);
            if (!empty($res)) {
                $log_data=[
                    'log_userid'=>$uid,
                    'log_orderid'=>'',
                    'log_mail'=>'',
                    'log_text'=>'系统赠送VIP'.$vde['vip_name'].",".$vipday."天",
                    'log_time'=>time(),
                ];
               
                $add_log=Db::name('viplog')->insert($log_data);
                $this->success("赠送成功");
            }else{
               $this->error("赠送失败");
            }
        }
    }
    public function qxvip()
    {
        $uid=(int)input('post.uid');
        $re=Db::name('account')->where('id',$uid)->find();
        if (empty($re)) {
            $this->error("用户不存在");
        }
        if ($re['is_vip']==1) {
            $this->error("该用户不是VIP，无需修改");
        }
        $uservipcountmodel = Db::name('config')->where('dsp_name', 'user_count')->value('dsp_value');
        $data['is_vip']=1;
        $data['vip_type']=0;
        $data['vip_endtime']=$re['vip_begintime'];
        $data['day_count']=$uservipcountmodel;
        $re=Db::name('account')->where('id',$uid)->update($data);
        if ($re) {
            $log_data=[
                    'log_userid'=>$uid,
                    'log_orderid'=>'',
                    'log_mail'=>'',
                    'log_text'=>'系统取消用户VIP身份',
                    'log_time'=>time(),
                ];
            $add_log=Db::name('viplog')->insert($log_data);
            $this->success("取消成功");
        }else{
            $this->error("取消失败");
        }
    }
    public function delno()
    {
        $re=Db::name('account')->where('status',0)->delete();
        if (!empty($re)) {
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }
}