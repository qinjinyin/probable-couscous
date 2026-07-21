<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Session;
class Login extends Controller
{
    //检查是否登录
    public function _initialize()
    {
        $request = \think\Request::instance();
        $contrname = $request->controller();
        $actionname = $request->action();
        $this->assign('contrname',$contrname);
        $this->assign('actionname',$actionname);
        if(session('userid')&&session('mail')){
            $this->error('您已登录',"/user.html");
        }
    }
    public function index()
    {
    	
    	$this->getingfo();
        return $this->fetch();
    }
    public function register()
    {
    	$this->getingfo();
    	return $this->fetch();
    }
    public function ajaxlogin()
    {
        if (!$this->request->isPost()) {
            $this->error("参数错误");
        }else{
            $mail=remove_xss(input('post.mail'));
            $userinfo=Db::name('account')->where('email',$mail)->find();
            if (empty($userinfo)) {
                $this->error("账号不存在");
            }
            $pass=remove_xss(input('post.pass'));
            $pass=dsp_password($pass,$authCode='');
            if ($pass!=$userinfo['email_password']) {
                $this->error("密码错误");
            }else if($userinfo['status']==0){
                $this->error("账号未激活(请检查邮箱验证码)或已被禁用");
            }else{
                Session::set('userid',$userinfo['id']);
                Session::set('mail',$userinfo['email']);
                $data['lasttime']=strtotime(date('y:m:d'));
                $re=Db::name('account')->where('email',$mail)->update($data);
                $login_log=[
                    'login_userid'  => session('userid'),
                    'login_time'    =>$data['lasttime'],
                    'login_ip'  =>GetIP(),
                ];
                $loginlog=Db::name('loginlog')->insert($login_log);
                $this->success("登录成功","/user.html"); 
            }
        }
    }
    

    public function ajaxregister()
    {
    	if (!$this->request->isPost()) {
    		$this->error("参数错误");
    	}else{
            //判断用户IP限制住注册
            $useripmodel = Db::name('config')->where('dsp_name', 'user_ip')->value('dsp_value');
            //获取用户IP
            $ip=get_ip();
            if ($useripmodel!=0) {
                $c=Db::name('account')->where('reg_ip',get_ip())->count();
                if ($c>$useripmodel) {
                    $this->error("当前IP已达到最高注册次数");
                    die;
                }
            }
            $mail=remove_xss(input('post.mail'));
    		$pass=remove_xss(input('post.pass'));
            //密码加密
            $pass=dsp_password($pass,$authCode='');
    		$qq=remove_xss(input('post.qq'));
            $res = Db::name('account')->where('email', $mail)->find();
            if (!empty($res)) {
                $this->error("该邮箱已被注册");
            }
            //判断是否开启邮箱验证
            $usermailmodel = Db::name('config')->where('dsp_name', 'user_mail')->value('dsp_value');
            $this->assign('usermailmodel',$usermailmodel);
            if ($usermailmodel==0) {
                //开启邮箱验证以后的操作
                //生成token
                $token=$this->makeToken($mail);
                //生成验证链接
                $active_url = (isHTTPS() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']."/valRegister?token=";//获取域名;
                
                if ($this->sendmail($mail,$token,$active_url,$pass)) {
                	$this->success("发送成功，请到邮箱查看！");
				}else{
					$this->error("发送失败");
                }

            }else{
                
                //查看注册会员每日多少次解析次数
                $usercountmodel = Db::name('config')->where('dsp_name', 'user_count')->value('dsp_value');
                //无开启验证直接注册
                $data = [
                    'email'          => $mail,
                    'email_password' => $pass,
                    'token'          => '1',
                    'regtime'        => time(),
                    'token_exptime'  => '1',
                    'status'  => 1,
                    'day_count'  => $usercountmodel,
                    'reg_ip'    => get_ip()
                ];
                $res  = Db::name('account')->insert($data);
                if (!empty($res)) {
                    $this->success("注册成功");
                }else{
                    $this->error("注册失败");
                }
            }
        }
    }
    //制作token
    public function makeToken($email)
    {
        $regtime = time();
        $num     = rand(0, 100);//一段随机数字
        $md5Num  = md5($regtime . $num . $email);
        $token   = base64_encode(md5($md5Num)); //创建用于激活识别码
 		return $token;
    }
    //邮箱激活方法--并且将邮箱的各个信息存放数据库
    public function valRegister()
    {
        $this->getingfo();
        //$token
        $nowtime = time(); //当前时间
        if ($this->request->isGet()) {
            $token = $this->request->param('token');
            //将条件token值与status=0状态值带入数据库查询,如果能查到，在判断时间是够是过期，就进行激活操作，改变激活码

            $res = Db('account')
                ->where('status', 0)
                ->where('token', $token)->find();
            
            if ($res) {
                if ($nowtime > $res['token_exptime']) {

                	$s_data='您的激活有效期已过，请登录您的帐号重新发送激活邮件😥';
	                $s_url='/register.html';
	                $this->assign('s_data',$s_data);
	                $this->assign('s_url',$s_url);
	                return $this->fetch('error');
                } else {
                    Db::name('account')->where('token', $token)->setField('status', 1);
                    $s_data='账号激活成功🤨';
                    $s_url='/login.html';
                    $this->assign('s_data',$s_data);
                    $this->assign('s_url',$s_url);
                    return $this->fetch('success');
                    //$this->success('恭喜您，激活成功！<br/>请进行登录！', url('user/login/index'));
                }
            } else {
            	$s_data='邮箱不存在或已激活😥';
                $s_url='/register.html';
                $this->assign('s_data',$s_data);
                $this->assign('s_url',$s_url);
                return $this->fetch('error');
                //$this->error('邮箱注册失败！请检查邮箱号码是否正确', url('user/login/reister'));
            }
        }
        
    }
    //发送qq邮箱
    /*
     * @param
     *  $address_email --收件人邮箱
     *  $active_url ---激活地址
     *  $token --- 账户激活码
     *  $email_password --邮箱密码
     * **/
    function sendmail($address_email, $token, $active_url, $email_password)
    {   
        //查询邮件配置
		$mailconfig=Db::name('mail_config')->where('id',1)->find();
		Vendor('phpmailer.phpmailer'); //引入扩展类文件
    	$sendmail = $mailconfig['send_sys_mail']; //发件人邮箱
    	$sendmailpswd = $mailconfig['send_sys_pwd']; //客户端授权密码
    	$send_name    = $mailconfig['send_sys_name'];// 设置发件人信息
    	$toemail      = $address_email;//定义收件人的邮箱
    	$to_name      = $address_email;//设置收件人信息，如邮件格式说明中的收件人
    	$mail = new \phpmailer\phpmailer(); //实例化
        $mail->isSMTP();// 使用SMTP服务
        $mail->CharSet    = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码
        $mail->Host       = $mailconfig['send_sys_smtp'];// 发送方的SMTP服务器地址
        $mail->SMTPAuth   = true;// 是否使用身份验证
        $mail->Username   = $sendmail;//// 发送方的
        $mail->Password   = $sendmailpswd;//客户端授权密码,而不是邮箱的登录密码！
        $mail->SMTPSecure = "ssl";// 使用ssl协议方式
        $mail->Port       = (int)$mailconfig['send_sys_port'];//  qq端口465或587）
        $mail->setFrom($sendmail, $send_name);
        $mail->addAddress($toemail, $to_name);// 设置收件人信息，如邮件格式说明中的收件人，
        $mail->addReplyTo($sendmail, $send_name);// 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址
        $mail->Subject = $mailconfig['send_sys_name'].",激活邮箱";// 邮件标题
        $mail->Body = "恭喜您，注册成功！请点击链接激活您的帐户:".(isHTTPS() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']."/valRegister.html?token=$token"."\r\n"."如果以上链接无法点击，请将它复制到你的浏览器地址栏中进入访问，该链接24小时内有效。";// 邮件正文
    	$token_exptime = time() + 60 * 60 * 24;//过期时间为24小时后
    	if (!$mail->send()) {// 发送邮件
    		return false;
            //$this->error('邮箱注册失败！请检查邮箱号码是否正确', url('user/register/index'));
        }else{
        	//查看注册会员每日多少次解析次数
            $usercountmodel = Db::name('config')->where('dsp_name', 'user_count')->value('dsp_value');
            $data = [
	            'email'          => $address_email,
	            'email_password' => $email_password,
	            'token'          => $token,
	            'regtime'        => time(),
	            'token_exptime'  => $token_exptime,
                'day_count'  => (int)$usercountmodel,
                'reg_ip'    => get_ip()
	        ];
        	$res  = Db::name('account')->insert($data);
        	return true;
        }
    }
    function getingfo()
    {
    	$info=Db::name('info')->where('id',1)->find();
    	$this->assign('info',$info);
    }
}
