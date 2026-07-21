<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Vip extends Controller
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
       
        $viplist=Db::name('viptype')->select();
        $this->assign('viplist',$viplist);
        return $this->fetch();
    }
    public function changeStatus()
    {
        $pid=(int)$_POST['id'];
        $p=Db::name('viptype')->where('id',$pid)->find();
        if (empty($p)) {
            $this->error("VIP方式不存在");
        }
        $sta['vip_is_tj']=(int)$_POST['status'];
        $re=Db::name('viptype')->where('id',$pid)->update($sta);
        if (empty($p)) {
            $this->error("设置失败");
        }else{
            $this->success("设置成功");
        }
    }
    public function setvip()
    {
        $id=(int)$_GET['id'];
        $re=Db::name("viptype")->where("id",$id)->find();
        if (empty($re)) {
            $this->error("会员方式不存在");
        }else{
            $this->assign('re',$re);
        }
        return $this->fetch();
    }
    public function editvip()
    {
        $vid=(int)remove_xss(input('post.vid'));
        $data['vip_name']=remove_xss(input('post.vip_name'));
        $data['vip_img']=remove_xss(input('post.vip_img'));
        $data['vip_desc']=input('post.textv');
        $data['vip_price']=remove_xss(input('post.vip_price'));
        $data['vip_pirce_old']=remove_xss(input('post.vip_pirce_old'));
        $data['vip_day']=(int)remove_xss(input('post.vip_day'));
        $data['vip_is_tj']=(int)remove_xss(input('post.istj'));
        if (empty($vid)) {
            $this->error("数据错误");
        }
        if (empty($data['vip_name'])) {
            $this->error("VIP名称不能为空");
        }
        if (empty($data['vip_img'])) {
            $this->error("VIP图片不能为空");
        }
        if (empty($data['vip_price'])) {
            $this->error("VIP价格不能为空");
        }
        if (empty($data['vip_day'])) {
            $this->error("VIP时长不能为空");
        }
        $re=Db::name('viptype')->where('id',$vid)->update($data);
        if (empty($re)) {
            $this->error("修改失败");
        }else{
            $this->success("修改成功");
        }
    }
}