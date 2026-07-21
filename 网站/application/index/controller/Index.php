<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
class Index extends Controller
{
    //检查是否登录
    public function _initialize()
    {
        $request = \think\Request::instance();
        $contrname = $request->controller();
        $actionname = $request->action();
        $this->assign('contrname',$contrname);
        $this->assign('actionname',$actionname);
        
        
    }
    public function index()
    {

    	$info=Db::name('info')->where('id',1)->find();
    	$this->assign('info',$info);
    	//首页公告模块是否开启
    	$indexnoticemodel = Db::name('config')->where('dsp_name', 'index_notice')->value('dsp_value');
    	$this->assign('indexnoticemodel',$indexnoticemodel);
    	if ($indexnoticemodel==0) {
    		$notice=Db::name('notice')->where('id',1)->find();
    		$this->assign('notice',$notice);
    	}
    	//首页广告模块是否开启
    	$indexggmodel = Db::name('config')->where('dsp_name', 'index_gg')->value('dsp_value');
    	$this->assign('indexggmodel',$indexggmodel);
    	if ($indexggmodel==0) {
    		$glist=Db::name('gg')->where('g_sta',1)->order('g_sort desc')->select();
    	$this->assign('glist',$glist);
    	}
    	//首页最近解析模块是否开启
    	$indexjxmodel = Db::name('config')->where('dsp_name', 'index_zjjx')->value('dsp_value');
    	$this->assign('indexjxmodel',$indexjxmodel);
    	if ($indexjxmodel==0) {
    		$jxlog=Db::name('jxlog')->order('id desc')->limit(9)->select();
    		$this->assign('jxlog',$jxlog);
    	}
        
        return $this->fetch();
    }
}
