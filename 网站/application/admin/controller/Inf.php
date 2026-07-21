<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;
class Inf extends Controller
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

        $apilist= Db::name('interface')->where("is_del",0)->select();
        $this->assign('apilist',$apilist);
        return $this->fetch();
    }
    public function changeStatus()
    {
        $pid=(int)$_POST['id'];
        $p=Db::name('interface')->where('id',$pid)->find();
        if (empty($p)) {
            $this->error("接口不存在");
        }
        $sta['api_sta']=(int)$_POST['status'];
        $re=Db::name('interface')->where('id',$pid)->update($sta);
        if (empty($p)) {
            $this->error("设置失败");
        }else{
            $this->success("设置成功");
        }
    }
    public function huishou()
    {
        $apilist= Db::name('interface')->where("is_del",1)->select();
        $this->assign('apilist',$apilist);
        return $this->fetch();
    }
    public function addinf()
    {
        return $this->fetch();
    }
    public function editinf()
    {
        $id=(int)$_GET['id'];
        $inf=Db::name('interface')->where('id',$id)->find();

        if (empty($inf)) {
            $this->error("接口不存在");
        }
        $this->assign('inf',$inf);
        return $this->fetch();
    }
    public function ajaxaddinf()
    {
        $act=remove_xss(input('post.act'));

        $data = [
            'api_title' => remove_xss(input('post.apiname')),
            'api_sta' => remove_xss(input('post.isuse')),
            'api_apiimg' => remove_xss(input('post.apiimg')),
            'api_local' => remove_xss(input('post.islocal')),
            'api_bs' => remove_xss(input('post.apibs')),
            'api_url' => remove_xss(input('post.apiurl')),
            'api_return_data' => remove_xss(input('post.rarray')),
            'api_return_img' => remove_xss(input('post.rimg')),
            'api_return_video' => remove_xss(input('post.rvideo')),
            'api_return_title' => remove_xss(input('post.rtitle')),
        ];
        if ($act=='add') {
            $re=Db::name('interface')->insert($data);
            if (!empty($re)) {
                $this->success("新增成功");
            }else{
                $this->error("新增失败");
            }
        }
        if ($act='edit') {
            $iid=(int)remove_xss(input('post.iid'));
            $re=Db::name('interface')->where('id',$iid)->update($data);
            if (!empty($re)) {
                $this->success("修改成功");
            }else{
                $this->error("修改失败");
            }
        }
    }
    public function ajaxupstatus()
    {
        $id=(int)$_POST['id'];
        $re=Db::name('interface')->where('id',$id)->find();
        if ($re['is_del']==1) {
            $this->error("该接口不存在");
        }
        $data['is_del']=1;
        $res=Db::name('interface')->where('id',$id)->update($data);
        if (!empty($res)) {
            $this->success("删除成功！");
        }else{
            $this->error("删除失败！");
        }
    }
    public function ajaxupstatush()
    {
        $id=(int)$_POST['id'];
        $re=Db::name('interface')->where('id',$id)->find();
        if ($re['is_del']==0) {
            $this->error("该接口不存在");
        }
        $data['is_del']=0;
        $res=Db::name('interface')->where('id',$id)->update($data);
        if (!empty($res)) {
            $this->success("还原成功！");
        }else{
            $this->error("还原失败！");
        }
    }
}