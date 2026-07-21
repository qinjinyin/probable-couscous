<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Order extends Controller
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

        $orderlist=Db::name('order')->select();
        $this->assign('orderlist',$orderlist);
        return $this->fetch();
    }
    public function noindex()
    {
        $orderlist=Db::name('order')->where("order_ispay",0)->select();
        $this->assign('orderlist',$orderlist);
        return $this->fetch();
    }
    public function deleteorder()
    {
        $id=(int)input('post.id');
        $re=Db::name('order')->where("id",$id)->find();
        if (!$re) {
            $this->error("订单不存在");
        }else{
            $re=Db::name('order')->where("id",$id)->delete();
            if ($re) {
                $this->success("删除成功");
            }else{
                $this->error("删除失败");
            }
        }
    }
    public function delno()
    {
        $re=Db::name('order')->where('order_ispay',0)->delete();
        if (!empty($re)) {
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }
}