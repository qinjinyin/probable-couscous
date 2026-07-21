<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;
class Gao extends Controller
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
        $glist=Db::name('gg')->order('g_sort desc')->select();
        $this->assign('glist',$glist);
        return $this->fetch();
    }
    public function addgg()
    {
        return $this->fetch();
    }
    public function editgg()
    {
        $id=(int)input('get.id');
        $p=Db::name('gg')->where('id',$id)->find();
        if (empty($p)) {
            $this->error("广告不存在");
        }
        $this->assign('p',$p);
        return $this->fetch();
    }
    public function ajaxaddgg()
    {
        //验证
        $file = request()->file('img');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                //$img_src = '/uploads/'.$info->getSaveName();

                $img_src='/public/uploads/'.date('Ymd')."/".$info->getfileName();
                $data['g_img']=$img_src;
                $data['g_url']=remove_xss(input('post.g_url'));
                $data['g_addtime']=time();
                $data['g_sort']=(int)remove_xss(input('post.g_sort'));
                $re=Db::name('gg')->insert($data);
                if ($re) {
                    $this->success("添加成功",url('index'));
                }else{
                    $this->error("添加失败");
                }
                
            }else{
              // 上传失败获取错误信息
                $this->error($file->getError());
            }
        }
    }
    public function ajaxeditgg()
    {
        //验证
        $id=(int)remove_xss(input('post.id'));
        $file = request()->file('img');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if(empty($file)){
                
                $data['g_url']=remove_xss(input('post.g_url'));
                $data['g_sort']=(int)remove_xss(input('post.g_sort'));
                $re=Db::name('gg')->where("id",$id)->update($data);
                if ($re) {
                    $this->success("修改成功",url('index'));
                }else{
                    $this->error("修改失败");
                }
            }else{
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if($info){
                $img_src='/public/uploads/'.date('Ymd')."/".$info->getfileName();
                $data['g_img']=$img_src;
                $data['g_url']=remove_xss(input('post.g_url'));
                $data['g_sort']=(int)remove_xss(input('post.g_sort'));
                $re=Db::name('gg')->where("id",$id)->update($data);
                if ($re) {
                    $this->success("修改成功",url('index'));
                }else{
                    $this->error("添加失败");
                }
                
            }
        }
    }
    public function delgg()
    {
        $pid=(int)$_POST['id'];
        $p=Db::name('gg')->where('id',$pid)->find();
        if (empty($p)) {
            $this->error("广告不存在");
        }
        $res=unlink('./'.$p['g_img']);
        $re=Db::name('gg')->where('id',$pid)->delete();
        if ($re) {
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }

    public function changeStatus()
    {
        $pid=(int)$_POST['id'];
        $p=Db::name('gg')->where('id',$pid)->find();
        if (empty($p)) {
            $this->error("广告不存在");
        }
        $sta['g_sta']=(int)$_POST['status'];
        $re=Db::name('gg')->where('id',$pid)->update($sta);
        if (empty($p)) {
            $this->error("设置失败");
        }else{
            $this->success("设置成功");
        }
    }
}