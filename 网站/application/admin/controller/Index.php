<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Cache;
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
        if(!session('adminid')){
            $this->error('请先登录！',"/admin/login.html");
        }
    }
    public function index()
    {
    	
        //用户总数
        $usercount=Db::name("account")->count();
        $this->assign('usercount',$usercount);
        //今日订单总数
        $ordercount=Db::name('order')->whereTime('order_creat_time','today')->count();
        $this->assign('ordercount',$ordercount);
        //平台总收入
        $ispaycount=Db::name('order')->where('order_ispay',1)->sum('order_price');
        $ispaycount = sprintf("%.2f",$ispaycount);
        
        $this->assign('ispaycount',$ispaycount);
        //接口数量
        $apicount=Db::name('interface')->where('is_del',0)->count();
        $this->assign('apicount',$apicount);

        $userlist=Db::name('account')->limit(5)->order("id DESC")->select();
        $this->assign('userlist',$userlist);
        //获取更新日志
        $url="https://au/getlog";
        $updata=$this->getCheck($url);
        $data=json_decode($updata,true);
        
        $this->assign('data',$data['data']);
        return $this->fetch();
    }
    //退出登录
    public function loginout()
    {
        session('adminid',null);
        session('adminaccount',null);
        session('adminname',null);
        session('adminqq',null);
        $this->success('退出成功',"/admin/login.html");
    }
    public function clearCache()
    {
        Cache::clear();  
        array_map( 'unlink', glob( TEMP_PATH.DS.'.php' ));
        
        $this->delFileByDir(TEMP_PATH);
        $this->success( '清除成功', 'index/index' );  
        
    }
    function delFileByDir($dir) {
       $dh = opendir($dir);
       while ($file = readdir($dh)) {
          if ($file != "." && $file != "..") {

             $fullpath = $dir . "/" . $file;
             if (is_dir($fullpath)) {
                delFileByDir($fullpath);
             } else {
                unlink($fullpath);
             }
          }
       }
       closedir($dh);
    }

    function getCheck($url){
      
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }
    
}
