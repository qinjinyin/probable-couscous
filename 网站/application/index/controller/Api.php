<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
class Api extends Controller
{
	public function index(){
		$sta=100;
		$msg='欢迎使用本系统';
		$title='';
		$url='';
		$cover='';
		$this->json_return($sta,$msg,$title,$url,$cover);
		die;
	}
	public function jiexi()
	{
		$userid=session('userid');

		//未注册用户是否可以直接使用
    	$apiis_loginmodel = Db::name('config')->where('dsp_name', 'is_login')->value('dsp_value');
    	if ($apiis_loginmodel==1) {
    		if (empty($userid)) {
    			$sta=106;
				$msg='请登录后使用';
				$title='';
				$url='';
				$cover='';
				$this->json_return($sta,$msg,$title,$url,$cover);
				die;
    		}else{
	    		$user_count=Db::name('account')->where('id',$userid)->find();
		    	$user_count=$user_count['day_count'];
		    	if ($user_count<=0) {
		    		$sta=110;
					$msg='解析次数不足';
					$title='';
					$url='';
					$cover='';
					$this->json_return($sta,$msg,$title,$url,$cover);
					die;
		    	}
				if (!$this->request->isPost()) {
					$sta=102;
					$msg='提交方式错误';
					$title='';
					$url='';
					$cover='';
					$this->json_return($sta,$msg,$title,$url,$cover);
					die;
				}
				//移除url变量xss攻击
				$url=trim(input('post.url'));
				
				//url正则匹配
				$str_r= '/([hH][tT]{2}[pP]:\/\/|[hH][tT]{2}[pP][sS]:\/\/)(([A-Za-z0-9-~]+).)+([A-Za-z0-9-~\/])+$/';
				if(!preg_match_all($str_r,$url,$arr)){
					$sta=103;
					$msg='请输入正确的链接';
					$title='';
					$url='';
					$cover='';
					$this->json_return($sta,$msg,$title,$url,$cover);
					die;
				}
				
				$url=$arr[0][0];
				
				$bs=explode('.', $url);
				//获取标识
				
				if(strstr($url, 'jimeng.jianying')){
					$bs='jimeng';
				}else if(strstr($url, 'doubao.com')){
					$bs='doubao';
				}else if(strstr($url, 'chenzhongtech')){
					$bs='chenzhongtech';
				}else if(strstr($url, 'kuaishouapp')){
					$bs='chenzhongtech';
				}else{
					$bs=$bs[1];
				}
				
				//查询是否存在接口
				$int=Db::name('interface')->where('api_bs',$bs)->find();
				
				if (empty($int)) {
					$sta=104;
					$msg='接口不存在';
					$title='';
					$url='';
					$cover='';
					$this->json_return($sta,$msg,$title,$url,$cover);
					die;
				}
				if ($int['api_sta']==0) {
					$sta=104;
					$msg='该接口已暂停使用';
					$title='';
					$url='';
					$cover='';
					$this->json_return($sta,$msg,$title,$url,$cover);
					die;
				}
				$is_local=(int)$int['api_local'];
				//判断接口是本地还是远程
				if ($is_local==0) {
					//本地接口
					//判断是否是本站请求
					$userip=$this->GetIP();
					$serverip=$_SERVER['REMOTE_ADDR'];
					if ($userip!=$serverip) {
						$sta=105;
						$msg='错误请求';
						$title='';
						$url='';
						$cover='';
						$this->json_return($sta,$msg,$title,$url,$cover);
						die;
					}else{
						$d=jiexi($url);
						$a_d=json_decode($d,true);
						$data = [
		                    'jx_url'	=> $url,
		                    'jx_img'	=> $a_d['data']['cover'],
		                ];
						$j_log=Db::name('jxlog')->insert($data);
						echo $d;
						$v_data=json_decode($d,true);
						if ($v_data['status']!=113) {
							# code...
							//解析成功 登录模块开启 执行次数操作
							if ($apiis_loginmodel==1) {
								$useri=Db::name('account')->where('id',$userid)->find();
								$c_data['day_count']=$useri['day_count']-1;
								$upu=Db::name('account')->where('id',$userid)->update($c_data);
							}
						}
						die;
				}
		}else{
				//远程接口 - 本地CLI解析
				$data=shell_exec('php8.2 /workspace/website/网站/api_parser/parse.php '.escapeshellarg($url));
				$a_data=$int['api_return_data'];
				$a_img=$int['api_return_img'];
				$a_video=$int['api_return_video'];
				$a_title=$int['api_return_title'];
			$arr_data=json_decode($data,true);
			$is_data_valid=false;
			if (!$arr_data || !isset($arr_data['code'])) {
				$this->json_return(107,'数据解析失败','','','');
				die;
			}
			if ($arr_data['code']!==200) {
				$this->json_return($arr_data['code'],$arr_data['msg']??'解析失败','','','');
				die;
			}
			if (isset($arr_data[$a_data]) && is_array($arr_data[$a_data])) {
				$arr=$arr_data[$a_data];
				if (isset($arr[$a_img]) && isset($arr[$a_video]) && isset($arr[$a_title])) {
					$d_img=$arr[$a_img];
					$d_video=$arr[$a_video];
					$d_title=$arr[$a_title];
					$data = [
		                    	'jx_url'=> $url,
		                    	'jx_img' => $d_img,
		                	];
		               		$j_log=Db::name('jxlog')->insert($data);
					if ($apiis_loginmodel==1) {
					}
					$sta=200;
					$msg='解析成功';
					$title=$d_title;
					$url=$d_video;
					$cover=$d_img;
					$extra=[];
					if(isset($arr['type'])) $extra['type']=$arr['type'];
					if(isset($arr['images'])) $extra['images']=$arr['images'];
					$this->json_return($sta,$msg,$title,$url,$cover,$extra);
					if ($apiis_loginmodel==1) {
						$useri=Db::name('account')->where('id',$userid)->find();
						$c_data['day_count']=$useri['day_count']-1;
						$upu=Db::name('account')->where('id',$userid)->update($c_data);
					}
					die;
				}
			}
			if (!$is_data_valid) {
				$this->json_return(107,'数据结构异常','','','');
				die;
			}
		}
	}
	}
	if (!$this->request->isPost()) {
			$sta=102;
			$msg='提交方式错误';
			$title='';
			$url='';
			$cover='';
			$this->json_return($sta,$msg,$title,$url,$cover);
			die;
		}
		//移除url变量xss攻击
		$url=remove_xss(trim(input('post.url')));
		//url正则匹配
		$str_r= '/(http:\/\/|https:\/\/)((\w|=|\?|\.|\/|&|-)+)/';
		if(!preg_match_all($str_r,$url,$arr)){
			$sta=103;
			$msg='请输入正确的链接';
			$title='';
			$url='';
			$cover='';
			$this->json_return($sta,$msg,$title,$url,$cover);
			die;
		}
		
		$url=$arr[0][0];
		$bs=explode('.', $url);
		//获取标识
		
			if(strstr($url, 'jimeng.jianying')){
				$bs='jimeng';
			}else if(strstr($url, 'doubao.com')){
				$bs='doubao';
			}else if(strstr($url, 'chenzhongtech')){
				$bs='chenzhongtech';
			}else if(strstr($url, 'kuaishouapp')){
				$bs='chenzhongtech';
			}else if(strstr($url, 'toutiao.com')){
				$bs='toutiaoimg';
			}else if(strstr($url, 'xiaohongshu.com')||strstr($url, 'xhslink.com')){
				$bs='xhs_parse';
			}else{
				$bs=$bs[1];
			}
			
			//查询是否存在接口
			$int=Db::name('interface')->where('api_bs',$bs)->find();
			
			if (empty($int)) {
				$sta=104;
				$msg='接口不存在';
				$title='';
				$url='';
				$cover='';
				$this->json_return($sta,$msg,$title,$url,$cover);
				die;
			}
			if ($int['api_sta']==0) {
				$sta=104;
				$msg='该接口已暂停使用';
				$title='';
				$url='';
				$cover='';
				$this->json_return($sta,$msg,$title,$url,$cover);
				die;
			}
			$is_local=(int)$int['api_local'];
			//判断接口是本地还是远程
			if ($is_local==0) {
				//本地接口
				//判断是否是本站请求
				$userip=$this->GetIP();
				$serverip=$_SERVER['REMOTE_ADDR'];
				if ($userip!=$serverip) {
					$sta=105;
					$msg='错误请求';
					$title='';
					$url='';
					$cover='';
					$this->json_return($sta,$msg,$title,$url,$cover);
					die;
				}else{
					
					$d=jiexi($url);
					$a_d=json_decode($d,true);
					$data = [
                        'jx_url'	=> $url,
                        'jx_img'	=> $a_d['data']['cover'],
                ];
				$j_log=Db::name('jxlog')->insert($data);
				echo $d;
				$v_data=json_decode($d,true);
				if ($v_data['status']!=113) {
					# code...
					//解析成功 登录模块开启 执行次数操作
					if ($apiis_loginmodel==1) {
						$useri=Db::name('account')->where('id',$userid)->find();
						$c_data['day_count']=$useri['day_count']-1;
						$upu=Db::name('account')->where('id',$userid)->update($c_data);
					}
				}
			}
		}else{
		//远程接口 - 本地CLI解析
			$cmd = 'php8.2 /workspace/website/网站/api_parser/parse.php '.escapeshellarg($url);
			$data=shell_exec($cmd);
			$a_data=$int['api_return_data'];
			$a_img=$int['api_return_img'];
			$a_video=$int['api_return_video'];
			$a_title=$int['api_return_title'];
			$arr_data=json_decode($data,true);
			$is_data_valid=false;
			if (!$arr_data || !isset($arr_data['code'])) {
				$this->json_return(107,'数据解析失败','','','');
				die;
			}
			if ($arr_data['code']!==200) {
				$this->json_return($arr_data['code'],$arr_data['msg']??'解析失败','','','');
				die;
			}
			if (isset($arr_data[$a_data]) && is_array($arr_data[$a_data])) {
				$arr=$arr_data[$a_data];
				if (isset($arr[$a_img]) && isset($arr[$a_video]) && isset($arr[$a_title])) {
					$d_img=$arr[$a_img];
					$d_video=$arr[$a_video];
					$d_title=$arr[$a_title];
					$data = [
                    	'jx_url'          => $url,
                    	'jx_img' => $d_img,
                	];
               		$j_log=Db::name('jxlog')->insert($data);
					if ($apiis_loginmodel==1) {
					}
				$sta=200;
				$msg='解析成功';
				$title=$d_title;
				$url=$d_video;
				$cover=$d_img;
				$extra=[];
				if(isset($arr['type'])) $extra['type']=$arr['type'];
				if(isset($arr['images'])) $extra['images']=$arr['images'];
				$this->json_return($sta,$msg,$title,$url,$cover,$extra);
				if ($apiis_loginmodel==1) {
						$useri=Db::name('account')->where('id',$userid)->find();
						$c_data['day_count']=$useri['day_count']-1;
						$upu=Db::name('account')->where('id',$userid)->update($c_data);
					}
					die;
				}
			}
			if (!$is_data_valid) {
				$this->json_return(107,'数据结构异常','','','');
				die;
			}
		}
	}
	function json_return($sta,$msg,$title,$url,$cover,$extra=[])
	{
		$str = array(
    		"code" => $sta,
        	"msg"  => $msg,
        	"data" => array(
            	"title"   =>$title,
            	"url"   =>$url,
            	"cover"   =>$cover,
				"bigFile"	=> false,
				"down"	=>$url,				
				"download_image"   =>$cover,
            )
        );
        if (!empty($extra)) {
            foreach ($extra as $k => $v) {
                $str['data'][$k] = $v;
            }
        }
        $str = json_encode($str);
    	echo $str; 
	}
	//获取用户端IP
	function GetIP(){
		if(!empty($_SERVER["HTTP_CLIENT_IP"]))
		$cip = $_SERVER["HTTP_CLIENT_IP"];
		else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
		$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		else if(!empty($_SERVER["REMOTE_ADDR"]))
		$cip = $_SERVER["REMOTE_ADDR"];
		else
		$cip = "";
		return $cip;
	}
	public function dsp(){
		//系统分配给用户的token
		$token=remove_xss($this->request->param('token'));
		$todayStart= strtotime(date('Y-m-d 00:00:00', time())); //当天时间
        $todayEnd= strtotime(date('Y-m-d 23:59:59', time())); //当天结束时间
		//用户id
		$userid=(int)remove_xss($this->request->param('userid'));
		//查询用户信息
		$userauth=Db::name('account')->where('api_token',$token)->where('id',$userid)->find();

		$vtype=$userauth['vip_type'];
		$userapiopenmodel=Db::name('config')->where('dsp_name', 'vip_int')->value('dsp_value');
		if ($vtype<$userapiopenmodel) {
            $sta=115;
			$msg='会员等级不足';
			$title='';
			$url='';
			$cover='';
			$this->json_return($sta,$msg,$title,$url,$cover);
			die;
        }
		//查询用户当天是否登录
		$loginlog=Db::name('loginlog')->where('login_userid',$userid)->where('login_time','gt',$todayStart)->where('login_time','lt',$todayEnd)->count();
		
		if ($loginlog==0) {
			if ($userauth['vip_type']==0) {
                $vtype='user_count';
            }elseif ($userauth['vip_type']==1) {
                $vtype='user_vip1';
            }elseif ($userauth['vip_type']==2) {
                $vtype='user_vip2';
            }
            elseif ($userauth['vip_type']==3) {
                $vtype='user_vip3';
            }
            $uservipcountmodel=Db::name('config')->where('dsp_name', $vtype)->value('dsp_value');
            $usercount['day_count']=$uservipcountmodel;
            $upusercount=Db::name('account')->where('id',$userid)->update($usercount);
            $login_log=[
                'login_userid'  => session('userid'),
                'login_time'    =>strtotime(date('y:m:d')),
                'login_ip'  =>GetIP(),
            ];
            $loginlog=Db::name('loginlog')->insert($login_log);
            $loginlog=Db::name('loginlog')->insert($login_log);
		}
		$userauth=Db::name('account')->where('api_token',$token)->where('id',$userid)->find();
		if ($userauth['day_count']<=0) {
			$sta=110;
			$msg='次数不足';
			$title='';
			$url='';
			$cover='';
			$this->json_return($sta,$msg,$title,$url,$cover);
			die;
		}
		if (empty($userauth)) {
			
			$sta=108;
			$msg='会员接口不存在';
			$title='';
			$url='';
			$cover='';
			$this->json_return($sta,$msg,$title,$url,$cover);
			die;
		}
		//短视频链接
		$url=remove_xss((input('get.url')));
		
		$userapiopenmodel=Db::name('config')->where('dsp_name', 'api_open')->value('dsp_value');
		if ($userapiopenmodel==0) {
			$sta=109;
			$msg='接口被系统管理员关闭';
			$title='';
			$url='';
			$cover='';
			$this->json_return($sta,$msg,$title,$url,$cover);
			die;
		}
		$str_r= '/(http:\/\/|https:\/\/)((\w|=|\?|\.|\/|&|-)+)/';
		if(!preg_match_all($str_r,$url,$arr)){
			$sta=103;
			$msg='请输入正确的链接';
			$title='';
			$url='';
			$cover='';
			$this->json_return($sta,$msg,$title,$url,$cover);
			die;
		}
		$url=$arr[0][0];
		$bs=explode('.', $url);
		//获取标识

		if(strstr($url, 'jimeng.jianying')){
			$bs='jimeng';
		}else if(strstr($url, 'doubao.com')){
			$bs='doubao';
		}else if(strstr($url, 'chenzhongtech')){
			$bs='chenzhongtech';
		}else if(strstr($url, 'kuaishouapp')){
			$bs='chenzhongtech';
		}else{
			$bs=$bs[1];
		}
		
		//查询是否存在接口
		$int=Db::name('interface')->where('api_bs',$bs)->find();
		if (empty($int)) {
			$sta=104;
			$msg='接口不存在';
			$title='';
			$url='';
			$cover='';
			$this->json_return($sta,$msg,$title,$url,$cover);
			die;
		}
		if ($int['api_sta']==0) {
			$sta=104;
			$msg='该接口已暂停使用';
			$title='';
			$url='';
			$cover='';
			$this->json_return($sta,$msg,$title,$url,$cover);
			die;
		}
		$is_local=(int)$int['api_local'];
		//判断接口是本地还是远程
		//本地接口
		if ($is_local==0) {
			$d=jiexi($url);
            //解析成功 减去用户次数 api解析的不写进解析日志
			$user_data['day_count']=$userauth['day_count']-1;

            $upu=Db::name('account')->where('id',$userid)->update($user_data);

			echo $d;
			die;
		}
		//远程接口
		if ($is_local==1) {
			//远程接口
			//组装url
			$api_url=$int['api_url'];
			$api_url=$int['api_url'].$url;
			$data=msCurlGet($api_url);
			$a_data=$int['api_return_data'];
			$a_img=$int['api_return_img'];
			$a_video=$int['api_return_video'];
			$a_title=$int['api_return_title'];
			$arr_data=json_decode($data,true);
			if (array_key_exists($a_data, $arr_data)) {
				$arr=$arr_data[$a_data];
				if (array_key_exists($a_img, $arr)) {
					$d_img=$arr[$a_img];
					if (array_key_exists($a_video, $arr)) {
						$d_video=$arr[$a_video];
						if (array_key_exists($a_title, $arr)) {
							//解析成功 登录模块开启 执行次数操作
							$d_title=$arr[$a_title];
							$user_data['day_count']=(int)$userauth['day_count']-1;
            				$upu=Db::name('account')->where('id',$userid)->update($user_data);
							$sta=200;
							$msg='解析成功';
							$title=$d_title;
							$url=$d_video;
							$cover=$d_img;
							$this->json_return($sta,$msg,$title,$url,$cover);
							die;
						}
					}
				}
		}else{
				echo json_encode(['st'=>107,'m'=>'数据结构异常','t'=>'','u'=>'','c'=>'']);
				die;
				$d_title=$arr[$a_title];
				$sta=107;
				$msg='数据结构异常';
				$title='';
				$url='';
				$cover='';
				$this->json_return($sta,$msg,$title,$url,$cover);
				die;
			}
		}
	}
}