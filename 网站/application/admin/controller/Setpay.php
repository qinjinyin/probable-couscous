<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;
class Setpay extends Controller
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

    	$paylist=Db::name('paytype')->where('is_del',0)->select();
    	$this->assign('paylist',$paylist);
    	return $this->fetch();
    }
    public function ajaxupstatus()
    {
    	$id=(int)$_POST['id'];
        $re=Db::name('paytype')->where('id',$id)->find();
        if ($re['is_del']==1) {
            $this->error("该接口不存在");
        }
        $data['is_del']=1;
        $res=Db::name('paytype')->where('id',$id)->update($data);
        if (!empty($res)) {
            $this->success("删除成功！");
        }else{
            $this->error("删除失败！");
        }
    }
    public function changeStatus()
    {
    	$pid=(int)$_POST['id'];
    	$p=Db::name('paytype')->where('id',$pid)->find();
    	if (empty($p)) {
    		$this->error("支付通道不存在");
    	}
    	$sta['is_use']=(int)$_POST['status'];
    	$re=Db::name('paytype')->where('id',$pid)->update($sta);
    	if (empty($p)) {
    		$this->error("设置失败");
    	}else{
    		$this->success("设置成功");
    	}
    }
    public function setpay()
    {
    	$id=(int)$_GET['id'];
    	$pd=Db::name('paytype')->where('id',$id)->find();
    	if (empty($pd)) {
    		$this->error("支付通道不存在");
    	}
    	$this->assign('pd',$pd);
    	return $this->fetch();
    }
    public function huishou()
    {
    	$paylist= Db::name('paytype')->where("is_del",1)->select();
        $this->assign('paylist',$paylist);
        return $this->fetch();
    }
    public function ajaxupstatush()
    {
    	$id=(int)$_POST['id'];
        $re=Db::name('paytype')->where('id',$id)->find();
        if ($re['is_del']==0) {
            $this->error("该接口不存在");
        }
        $data['is_del']=0;
        $res=Db::name('paytype')->where('id',$id)->update($data);
        if (!empty($res)) {
            $this->success("还原成功！");
        }else{
            $this->error("还原失败！");
        }
    }
    public function editpay()
    {
        $pid=(int)remove_xss(input('post.pid'));
        $private_key=remove_xss(input('post.pay_private_key'));
        if (empty($private_key)) {
            $data['pay_name']=remove_xss(input('post.pay_name'));
            $data['pay_bs']=remove_xss(input('post.pay_bs'));
            $data['pay_desc']=remove_xss(input('post.pay_desc'));
            $data['pay_public_key']=remove_xss(input('post.pay_public_key'));
            $data['pay_appid']=remove_xss(input('post.pay_appid'));
            $data['is_use']=(int)remove_xss(input('post.isuse'));
            $data['pay_notify_url']=remove_xss(input('post.pay_notify_url'));
            $data['pay_return_url']=remove_xss(input('post.pay_return_url'));
        }else{
            $data['pay_name']=remove_xss(input('post.pay_name'));
            $data['pay_bs']=remove_xss(input('post.pay_bs'));
            $data['pay_desc']=remove_xss(input('post.pay_desc'));
            $data['pay_public_key']=remove_xss(input('post.pay_public_key'));
            $data['pay_private_key']=remove_xss(input('post.pay_private_key'));
            $data['pay_appid']=remove_xss(input('post.pay_appid'));
            $data['is_use']=(int)remove_xss(input('post.isuse'));
            $data['pay_notify_url']=remove_xss(input('post.pay_notify_url'));
            $data['pay_return_url']=remove_xss(input('post.pay_return_url'));
        }
        if (empty($pid)) {
            $this->error("数据错误");
        }
        if (empty($data['pay_name'])) {
            $this->error("支付名称不能为空");
        }
        if (empty($data['pay_bs'])) {
            $this->error("支付标识不能为空");
        }
        if (empty($data['pay_desc'])) {
            $this->error("支付描述不能为空");
        }
        if (empty($data['pay_public_key'])) {
            $this->error("支付公钥或者TOKEN不能为空");
        }
        if (empty($data['pay_appid'])) {
            $this->error("APPID不能为空");
        }
        $re=Db::name('paytype')->where('id',$pid)->update($data);
        if (empty($re)) {
            $this->error("修改失败");
        }else{
            $this->success("修改成功");
        }
    }
    public function setkm()
    {
        //获取VIP方式
        $viplist=Db::name('viptype')->select();
        $this->assign('viplist',$viplist);
        return $this->fetch();
    }
    public function addkm()
    {
        $viptype=(int)input('post.viptype');
        if (empty($viptype)) {
            $this->error("请选择VIP方式");
        }
        $kmnum=(int)input('post.kmnum');
        if (empty($kmnum)) {
            $this->error("生成数量不能为空");
        }
        for ($i=0; $i < $kmnum; $i++) { 
            $data['vtype']=$viptype;
            $data['kami']="VIP".get_string(18).date('dis').rand(10000,99999);
            $data['creatuser']="system";
            $data['creattime']=time();
            $re=Db::name('kami')->insert($data);
        }
        $this->success("生成成功");
    }
    public function kmlist()
    {
        $list = Db::name('kami')->paginate(10);
        
        // 把分页数据赋值给模板变量list
        $this->assign('list', $list);
        
        return $this->fetch();
    }
    public function dctxt()
    {
        if(request()->isPost()){
            try{    
                $result = Db::name('kami')->where('useuser',0)->select();
                $content="";                
                foreach($result as $k=>$d){
                    //使用[.=]拼接字符串
                    $content .=$result[$k]['kami']."\r\n";
                }
                //转成json数据
                echo json_encode($content);             
            } catch (\Exception $e) {
                echo json_encode('error');
            }                   
        }   

    }
    public function kmdel()
    {
        $id=(int)input('get.id');
        if (empty($id)) {
            $this->error("参数错误");
        }
        $kmd=Db::name('kami')->where('id',$id)->find();
        if ($kmd['useuser']!=0) {
            $user=Db::name('account')->where('id',$kmd['useuser'])->find();
            $this->assign('user',$user);
        }
        $this->assign('kami',$kmd);
        return $this->fetch();
    }
    public function deletekami()
    {
        $id=(int)input('post.id');
        if (empty($id)) {
            $this->error("参数错误");
        }
        $re=Db::name('kami')->where('id',$id)->delete();
        if ($re) {
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }
    public function deletekamiuse()
    {
        $id=(int)input('post.id');

        if (empty($id)) {
            $this->error("参数错误");
        }
        if ($id!=1) {
            $this->error("参数错误");
        }
        $re=Db::name('kami')->where('useuser','neq',0)->where('usetime','neq',0)->delete();
        if ($re) {
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }
}