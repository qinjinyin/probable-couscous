<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use Config;
class System extends Controller
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

        $re=Db::name('info')->where("id",1)->find();
        $this->assign('re',$re);
        return $this->fetch();
    }
    public function upinfo()
    {
        $data['logo']=remove_xss(input('post.logo'));
        $data['title']=remove_xss(input('post.title'));
        $data['keywords']=remove_xss(input('post.keywords'));
        $data['descc']=remove_xss(input('post.descc'));
        $data['banquan']=remove_xss(input('post.banquan'));
        $data['tcode']=$_POST['tcode'];
        $re=Db::name('info')->where("id",1)->update($data);
        if ($re) {
            $this->success("修改成功");
        }else{
            $this->error("修改失败");
        }
    }
    public function uppass()
    {
        $re=Db::name('admin')->where("id",1)->find();
        $this->assign('re',$re);
        return $this->fetch();
    }
    public function ajaxuppass()
    {
        $pass=remove_xss(input('post.password'));
        if (empty($pass)) {
            $data['username']=remove_xss(input('post.username'));
            $data['nickname']=remove_xss(input('post.nickname'));
            $data['qq']=remove_xss(input('post.qq'));
            $re=Db::name('admin')->where("id",1)->update($data);
            if ($re) {
                $this->success("修改成功");
            }else{
                $this->success("修改失败");
            }
        }else{
            $data['password']=dsp_password($pass);
            $data['username']=remove_xss(input('post.username'));
            $data['nickname']=remove_xss(input('post.nickname'));
            $data['qq']=remove_xss(input('post.qq'));
            $re=Db::name('admin')->where("id",1)->update($data);
            if ($re) {
                $this->success("修改成功",'/admin/index/loginout.html');
            }else{
                $this->success("修改失败");
            }
        }
    }
    public function notice()
    {
        $re=Db::name('notice')->where("id",1)->find();
        $this->assign('re',$re);
        return $this->fetch();
    }
    public function upnotice()
    {
        $data['title']=input('post.title');
        $data['notice']=input('post.text');
        $data['addtime']=time();
        $re=Db::name('notice')->where("id",1)->update($data);
        if ($re) {
            $this->success("修改成功");
        }else{
            $this->error("修改失败");
        }
       
    }
    public function mailconf()
    {
        $re=Db::name('mail_config')->where("id",1)->find();
        $this->assign('re',$re);
        return $this->fetch();
    }
    public function upmail()
    {
        $data['send_sys_mail']=remove_xss(input('post.send_sys_mail'));
        $data['send_sys_pwd']=remove_xss(input('post.send_sys_pwd'));
        $data['send_sys_name']=remove_xss(input('post.send_sys_name'));
        $data['send_sys_smtp']=remove_xss(input('post.send_sys_smtp'));
        $data['send_sys_port']=remove_xss(input('post.send_sys_port'));
        $re=Db::name('mail_config')->where("id",1)->update($data);
        if ($re) {
            $this->success("修改成功");
        }else{
            $this->error("修改失败");
        }
    }
    public function webconf()
    {
        $re=Db::name('config')->select();
        $this->assign('re',$re);
        return $this->fetch();
    }
    public function upconfig()
    {
        $data = input('post.');
        
        $sql="UPDATE ms_dsp_config SET dsp_value = CASE dsp_name 
            WHEN 'api_open' THEN ".$data['api_open']."
            WHEN 'index_gg' THEN ".$data['index_gg']."
            WHEN 'index_notice' THEN ".$data['index_notice']."
            WHEN 'index_zjjx' THEN ".$data['index_zjjx']."
            WHEN 'is_login' THEN ".$data['is_login']."
            WHEN 'user_count' THEN ".$data['user_count']."
            WHEN 'user_ip' THEN ".$data['user_ip']."
            WHEN 'user_mail' THEN ".$data['user_mail']."
            WHEN 'user_vip1' THEN ".$data['user_vip1']."
            WHEN 'user_vip2' THEN ".$data['user_vip2']."
            WHEN 'user_vip3' THEN ".$data['user_vip3']."
            WHEN 'vip_get' THEN ".$data['vip_get']."
            WHEN 'vip_int' THEN ".$data['vip_int']."
            END";
        
        $result = Db::execute($sql);
       
        if ($result) {
            $this->success("修改成功");
        }else{
            $this->error("修改失败");
        }
    }
    public function auth()
    {
        $key=\think\Config::get('key.auth_key'); 
        $this->assign('key',$key);

        return $this->fetch();
    }
    public function getauth()
    {
        $key=remove_xss(input('post.code'));
        if (empty($key)) {
            $this->error("KEY不能为空");
        }
        
        $url=$_SERVER['SERVER_NAME'];
        $re=curl_post($key,$url);
        
        $arr=json_decode($re,true);
        
        if ($arr['status']==104) {
            $this->error($arr['msg']);
        }
        if ($arr['status']==100) {
                # code...3
                $r_token=$arr['data'];
                $token=\think\Config::get('key.auth_token');
                
                $key_path='./application/extra/key.php';
                    $conf_str = <<<php
<?php
return [
    //站点授权KEY
    'auth_key'=>'{$key}',
    'auth_token'=>'{$arr['data']}'
];
php;
        if (file_exists($key_path)) {
                // 删除原配置文件
                unlink($key_path);
            }
            
            if(file_put_contents($key_path, $conf_str)){
                $this->success("正版授权");

            }else{
                $this->error("授权失败");
            }
        }
    }
    
}