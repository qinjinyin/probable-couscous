<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;
class Update extends Controller
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
        
        $bb=Db::name("info")->where("id",1)->find();
        $bb=$bb['bb'];
        $this->assign('bb',$bb);
    	return $this->fetch();
    }
    public function checkup()
    {
        $bb=remove_xss(input('post.bb'));
        $url="https://ys.5266s.cn/checkupdsp?bb=".$bb;
        $updata=$this->getCheck($url);

        $data=json_decode($updata,true);
        if ($data['status']==100) {
            $u_data['code']=1;
            $u_data['msg']="检查成功";
            $u_data['data']=array(
                'bb'=>$data['data']['bb'],
                'info'=>$data['data']['info'],
                'downurl'=>$data['data']['downurl'],
                'time'=>$data['data']['time']
            );
            return json($u_data);
        }
        //$this->success("111");
    }
    public function update()
    {
        $durl=remove_xss(input('post.durl'));
        $bb=remove_xss(input('post.bb'));

        $arr = parse_url($durl);
        $fname = $arr['path'];
        $arr1=explode("/",$fname);
        $save_dir = "Updata";//下载目录
        $filename = $arr1[2];//文件名称
        $res=$this->getFile($durl, $save_dir, $filename, 1);
        if (!empty($res)) {
            $zip = new \ZipArchive; 
            $re = $zip->open($res['save_path']); //打开下载好的文件

            $arr3=explode(".",$arr1[2]);
            
            //$state = rename($arr3[0],'111');
            
            $result= $zip->extractTo('./'); //解压到文件
           
            $zip->close();
            if ($result==TRUE) {
                $this->delDirAndFile('./runtime');
                $this->delDirAndFile('./Updata');
                $data['bb']=$bb;
                $re=Db::name('info')->where("id=1")->update($data);
                $this->success("更新成功");
            }else{
                $this->error("更新失败");
            }

        }
        
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
    function getFile($url, $save_dir = '', $filename = '', $type = 0) {
        if (trim($url) == '') {
            return false;
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir.= '/';
        }
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return false;
        }
        //获取远程文件所采用的方法
        if ($type) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
            $content = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $content = ob_get_contents();
            ob_end_clean();
        }
        $size = strlen($content);
        //文件大小
        $fp2 = @fopen($save_dir . $filename, 'a');
        fwrite($fp2, $content);
        fclose($fp2);
        unset($content, $url);
        ob_clean();
        flush();
        return array(
            'file_name' => $filename,
            'save_path' => $save_dir . $filename
        );
    }
    function delDirAndFile( $dirName ){
        if($handle=opendir($dirName)){
            while(false!==($item=readdir($handle))){
                if($item!="."&&$item!=".."){
                    if(is_dir("$dirName/$item")){
                        $this->delDirAndFile("$dirName/$item");
                    }else{
                        unlink("$dirName/$item");
                    }
                }
            }
            closedir($handle);
            rmdir($dirName);
        }
    }
}