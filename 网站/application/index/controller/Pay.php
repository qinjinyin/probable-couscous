<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
class Pay extends Controller
{
    public function _initialize()
    {
        $this->getingfo();
        $request = \think\Request::instance();
        $contrname = $request->controller();
        $actionname = $request->action();
        
        $this->assign('contrname',$contrname);
        $this->assign('actionname',$actionname);
        
    }
    //获取网站基本信息
    function getingfo()
    {
        $info=Db::name('info')->where('id',1)->find();
        $this->assign('info',$info);
    }
	//为了方便开发者开发，这里大致做一下讲解，这是是支付标识,支付所有的方式和回调都是在这个控制器去完成的
	//比如说你数据库中现在只有一个支付宝支付，标识为：'alipay'
	//然后判断支付标识，是什么就执行什么方法，开发者只需要去写对应的支付函数以及回调函数就可以了
	//开发者须知。执行请带 $p(支付方式ID),$v(开通会员方式ID)这两个参数
	//支付信息token啊 什么之类的你可以写在数据库
	//或者写在方法内都可以
	//懂tp框架的，不太清楚的可以参考我写的支付宝支付，每行都是有备注的！
	//说明一下，目前版本只做了支付宝当面付，如需要增加和修改的请联系唯一QQ：321808886，其余均为假冒！
	//如果你是拿着破解程序来的，我希望你不要打扰我，只接正版用户对接支付以及修改。
	//正版用户新接支付统一200！
	//不想花钱的朋友，就等版本更新，
	public function index()
	{
		if (!$this->request->isPost()) {
            $this->error("参数错误");
        }else{
            //先获取用户id
            $userid=session('userid');
            //支付方式
        	$paytype=input('post.paytype');

            //会员等级
        	$vid=(int)input('post.id');
            //查询用户当前是否是vip以及vip等级
            $u=Db::name('account')->where('id',$userid)->find();
            $userviptype=(int)$u['vip_type'];
            //用户vip等级小于等于当前vip等级提示错误信息
            if ($userviptype!=0) {
                //3  1
                if ($userviptype>$vid) {
                    $result_msg['status']=104;
                    $result_msg['msg']='等级不符';
                    $result_msg['url']='';
                    echo json_encode($result_msg,true);die;
                }
            }
        	//查询是否有支付方式
        	$p=Db::name('paytype')->where('pay_bs',$paytype)->where('is_use',1)->find();
        	if (empty($p)) {
        		$result_msg['status']=104;
                $result_msg['msg']='支付方式不存在';
                $result_msg['url']='';
                echo json_encode($result_msg,true);die;
        	}
        	
        	//重新定义$p
        	$p=$p['id'];
        	//查询是否有会员方式
        	$v=Db::name('viptype')->where('id',$vid)->find();

        	if (empty($v)) {
        		$result_msg['status']=104;
                $result_msg['msg']='会员方式不存在';
                $result_msg['url']='';
                echo json_encode($result_msg,true);die;
        	}
			$v=$v['id'];

        	if ($paytype=='alipaydmf') {
        		$this->alipaydmf($p,$v);
        	}
            if ($paytype=='ealipay') {
                require_once("vendor/epay/epay_submit.class.php");
                $paycon=Db::name('paytype')->where('pay_bs','ealipay')->find();
                //获取支付ID
                $epay['partner']=$paycon['pay_appid'];
                //获取支付秘钥
                $epay['key']=$paycon['pay_private_key'];
                //获取支付网站
                $epay['apiurl']=$paycon['pay_public_key'];
                //签名方式
                $epay['sign_type']    = strtoupper('MD5');
                //编码
                $epay['input_charset']= strtolower('utf-8');
                //判断http还是https
                $epay['transport']= $this->request->scheme();
                //异步通知
                $notify_url = $paycon['pay_notify_url'];
                //同步
                $return_url = $paycon['pay_return_url'];
                //订单号
                $out_trade_no = "PAY".date('ymdis').get_string(10).rand(10000,99999);
                $type = 'alipay';
                //根据$p(支付方式)查询支付详情
                $payconfig=Db::name('paytype')->where('id',$p)->where('is_use',1)->find();
                //根据$v(会员方式)查询会员详情
                $viptype=Db::name('viptype')->where('id',$v)->find();
                $name = "用户购买".$viptype['vip_name'];
                //付款金额
                $money = $viptype['vip_price'];
                //站点名称
                $sitename = "用户购买".$viptype['vip_name']."商品";
                $parameter = array(
                    "pid" => trim($epay['partner']),
                    "type" => $type,
                    "notify_url"    => $notify_url,
                    "return_url"    => $return_url,
                    "out_trade_no"  => $out_trade_no,
                    "name"  => $name,
                    "money" => $money,
                    "sitename"  => $sitename
                );
                $pay_data['order_id']=$out_trade_no;
                $pay_data['order_goods_id']=$viptype['id'];
                $pay_data['order_title']=$name;
                $pay_data['order_desc']=$sitename;
                $pay_data['order_price']=$money;
                $pay_data['order_creat_time']=time();
                $pay_data['order_paytype']='ealipay';
                $pay_data['order_creat_user']=$userid;
                $pdata=Db::name('order')->insert($pay_data);
                $alipaySubmit = new \AlipaySubmit($epay);
                $html_text = $alipaySubmit->buildRequestForm($parameter);
                echo $html_text;
                
            }
            if ($paytype=='eqqpay') {
                require_once("vendor/epay/epay_submit.class.php");
                $paycon=Db::name('paytype')->where('pay_bs','ealipay')->find();
                //获取支付ID
                $epay['partner']=$paycon['pay_appid'];
                //获取支付秘钥
                $epay['key']=$paycon['pay_private_key'];
                //获取支付网站
                $epay['apiurl']=$paycon['pay_public_key'];
                //签名方式
                $epay['sign_type']    = strtoupper('MD5');
                //编码
                $epay['input_charset']= strtolower('utf-8');
                //判断http还是https
                $epay['transport']= $this->request->scheme();
                //异步通知
                $notify_url = $paycon['pay_notify_url'];
                //同步
                $return_url = $paycon['pay_return_url'];
                //订单号
                $out_trade_no = "PAY".date('ymdis').get_string(10).rand(10000,99999);
                $type = 'qqpay';
                //根据$p(支付方式)查询支付详情
                $payconfig=Db::name('paytype')->where('id',$p)->where('is_use',1)->find();
                //根据$v(会员方式)查询会员详情
                $viptype=Db::name('viptype')->where('id',$v)->find();
                $name = "用户购买".$viptype['vip_name'];
                //付款金额
                $money = $viptype['vip_price'];
                //站点名称
                $sitename = "用户购买".$viptype['vip_name']."商品";
                $parameter = array(
                    "pid" => trim($epay['partner']),
                    "type" => $type,
                    "notify_url"    => $notify_url,
                    "return_url"    => $return_url,
                    "out_trade_no"  => $out_trade_no,
                    "name"  => $name,
                    "money" => $money,
                    "sitename"  => $sitename
                );
                $pay_data['order_id']=$out_trade_no;
                $pay_data['order_goods_id']=$viptype['id'];
                $pay_data['order_title']=$name;
                $pay_data['order_desc']=$sitename;
                $pay_data['order_price']=$money;
                $pay_data['order_creat_time']=time();
                $pay_data['order_paytype']='eqqpay';
                $pay_data['order_creat_user']=$userid;
                $pdata=Db::name('order')->insert($pay_data);
                $alipaySubmit = new \AlipaySubmit($epay);
                $html_text = $alipaySubmit->buildRequestForm($parameter);
                echo $html_text;
            }
            if ($paytype=='ewxpay') {
                require_once("vendor/epay/epay_submit.class.php");
                $paycon=Db::name('paytype')->where('pay_bs','ealipay')->find();
                //获取支付ID
                $epay['partner']=$paycon['pay_appid'];
                //获取支付秘钥
                $epay['key']=$paycon['pay_private_key'];
                //获取支付网站
                $epay['apiurl']=$paycon['pay_public_key'];
                //签名方式
                $epay['sign_type']    = strtoupper('MD5');
                //编码
                $epay['input_charset']= strtolower('utf-8');
                //判断http还是https
                $epay['transport']= $this->request->scheme();
                //异步通知
                $notify_url = $paycon['pay_notify_url'];
                //同步
                $return_url = $paycon['pay_return_url'];
                //订单号
                $out_trade_no = "PAY".date('ymdis').get_string(10).rand(10000,99999);
                $type = 'wxpay';
                //根据$p(支付方式)查询支付详情
                $payconfig=Db::name('paytype')->where('id',$p)->where('is_use',1)->find();
                //根据$v(会员方式)查询会员详情
                $viptype=Db::name('viptype')->where('id',$v)->find();
                $name = "用户购买".$viptype['vip_name'];
                //付款金额
                $money = $viptype['vip_price'];
                //站点名称
                $sitename = "用户购买".$viptype['vip_name']."商品";
                $parameter = array(
                    "pid" => trim($epay['partner']),
                    "type" => $type,
                    "notify_url"    => $notify_url,
                    "return_url"    => $return_url,
                    "out_trade_no"  => $out_trade_no,
                    "name"  => $name,
                    "money" => $money,
                    "sitename"  => $sitename
                );
                $pay_data['order_id']=$out_trade_no;
                $pay_data['order_goods_id']=$viptype['id'];
                $pay_data['order_title']=$name;
                $pay_data['order_desc']=$sitename;
                $pay_data['order_price']=$money;
                $pay_data['order_creat_time']=time();
                $pay_data['order_paytype']='ewxpay';
                $pay_data['order_creat_user']=$userid;
                $pdata=Db::name('order')->insert($pay_data);
                $alipaySubmit = new \AlipaySubmit($epay);
                $html_text = $alipaySubmit->buildRequestForm($parameter);
                echo $html_text;
            }
        }
	}
	//支付宝当面付
	function alipaydmf($p,$v)
	{	
		//先获取用户id
		$userid=session('userid');
		//引入支付宝类
		Vendor('alipay.f2fpay.model.builder.AlipayTradePrecreateContentBuilder');  
        Vendor('alipay.f2fpay.service.AlipayTradeService'); 
        //根据$p(支付方式)查询支付详情
        $payconfig=Db::name('paytype')->where('id',$p)->where('is_use',1)->find();
        //根据$v(会员方式)查询会员详情
        $viptype=Db::name('viptype')->where('id',$v)->find();
        //支付宝订单号
        $outTradeNo = "PAY".date('ymdis').get_string(10).rand(10000,99999);
        //订单标题
        $subject = "用户购买".$viptype['vip_name'];
        //订单总金额
        $totalAmount =$viptype['vip_price'];

        // 订单描述
        $body = "用户购买".$viptype['vip_name']."商品";
        //插入数据库，创建订单
        $data=[
        	'order_id' => $outTradeNo,
        	'order_goods_id' =>$viptype['id'],
        	'order_title'	=>$subject,
        	'order_desc'	=>$body,
        	'order_price'	=>$totalAmount,
        	'order_creat_time'	=>time(),
        	'order_paytype'	=>'alipay',
        	'order_creat_user'	=>$userid,
        ];
        $config=[
        	'sign_type' =>"RSA2",
        	'alipay_public_key'	=>$payconfig['pay_public_key'],
        	'merchant_private_key'	=>$payconfig['pay_private_key'],
        	'charset' => "UTF-8",
			//支付宝网关
        	'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
			//应用ID
	        'app_id' => $payconfig['pay_appid'],
			//异步通知地址,只有扫码支付预下单可用 当面付没啥用
	        'notify_url' => "https://xx.muzzz.cn",
			//最大查询重试次数
	        'MaxQueryRetry' => "10",
			//查询间隔
	        'QueryDuration' => "3"
        ];
        
        $re=Db::name('order')->insert($data);
        if (empty($re)) {
            $result['status']=104;
            $result['msg']='插入数据出错';
            $result['url']='';
            echo json_encode($result,true);die;
        }else{
            $timeExpress = "5m";
            $qrPayRequestBuilder = new \AlipayTradePrecreateContentBuilder();
            $qrPayRequestBuilder->setOutTradeNo($outTradeNo);
            $qrPayRequestBuilder->setTotalAmount($totalAmount);
            $qrPayRequestBuilder->setTimeExpress($timeExpress);
            $qrPayRequestBuilder->setSubject($subject);
            $qrPayRequestBuilder->setBody($body);
            $qrPay = new \AlipayTradeService($config);
            $qrPayResult = $qrPay->qrPay($qrPayRequestBuilder);
            switch ($qrPayResult->getTradeStatus()){
                case "SUCCESS":
                    // echo "支付宝创建订单二维码成功:"."<br>---------------------------------------<br>";
                    $response = $qrPayResult->getResponse();
                    $url = $response->qr_code;
                    //var_dump($url);die;
                    $result['status']=100;
                    $result['msg']='订单创建成功';
                    $result['url']=$url;
                    $result['orderid']=$outTradeNo;
                    $result['price']=$totalAmount;
                    echo json_encode($result,true);die;
                    break;
                case "FAILED":
                    $result['status']=101;
                    $result['msg']='订单创建失败';
                    $result['url']='';
                    echo json_encode($result,true);die;
                    break;
                case "UNKNOWN":
                    $result['status']=101;
                    $result['msg']='订单创建异常';
                    $result['url']='';
                    echo json_encode($result,true);die;
                    break;
                default:
                $result['status']=101;
                $result['msg']='不支持的方式';
                $result['url']='';
                echo json_encode($result,true);die;
                break;
            }
        }
	}
    //支付宝当面付 查单函数
    public function alipayquery()
    {
        //先获取用户id
        $userid=session('userid');
        $pid=input('post.id');

        //根据$p(支付方式)查询支付详情
        $payconfig=Db::name('paytype')->where('pay_bs',$pid)->where('is_use',1)->find();
        if (empty($payconfig)) {
            $result['status']=101;
            $result['msg']='不支持的方式';
            $result['url']='';
            echo json_encode($result,true);die;
            die;
        }
        Vendor('alipay.aop.AopClient');
        Vendor('alipay.aop.request.AlipayTradeQueryRequest'); 
        $aop = new \AopClient ();
        //var_dump($config['app_id']);
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $payconfig['pay_appid'];
        $aop->rsaPrivateKey = $payconfig['pay_private_key'];
        $aop->alipayrsaPublicKey=$payconfig['pay_public_key'];
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $request = new \AlipayTradeQueryRequest ();
        $out_trade_no = trim($_POST['outTradeNo']);

        $request->setBizContent("{" .
        "\"out_trade_no\":\"$out_trade_no\"" .
        "  }");
        $result = $aop->execute($request); 
        //var_dump($result);die;
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            $str=$result->$responseNode->trade_status;
            if ($str=='WAIT_BUYER_PAY') {
                $result_msg['status']=100;
                $result_msg['msg']='创建订单成功，等待付款';
                $result_msg['url']='';
                echo json_encode($result_msg,true);die;
            }elseif ($str=='TRADE_SUCCESS') {
                //根据订单号去查询订单
                $order=Db::name('order')->where("order_id",$out_trade_no)->where('order_ispay',0)->find();
                if (empty($order)) {
                    $result_msg['status']=104;
                    $result_msg['msg']='订单未找到或者已被支付';
                    $result_msg['url']='';
                    echo json_encode($result_msg,true);die;
                    die;
                }
                
                //查询开通的vip
                //vid是会员方式
                $vid=(int)$order['order_goods_id'];
                $vinfo=Db::name('viptype')->where('id',$vid)->find();
                //拿到会员标识
                $vbs=$vinfo['vip_bs'];
                //拿到会员时长
                $viptime=$vinfo['vip_day'];//时长
                //拿到会员价格
                $vipprice=$vinfo['vip_price'];//留着写日志
                //查询当前vip等级是多少次数
                $uservipcountmodel = Db::name('config')->where('dsp_name', $vbs)->value('dsp_value');
                //先查询用户是否开通过vip
                $u=Db::name('account')->where('id',$userid)->find();
                if ($u['is_vip']==1) {
                    //新开通
                    //修改次数，先查询次数
                    $vip_data=[
                        'is_vip'=>0,
                        'vip_type'  =>$vid,
                        'vip_begintime' =>time(),
                        'vip_endtime'   =>time()+$viptime*24*60*60,
                        'day_count'   =>$uservipcountmodel,
                    ];
                    $ktvip=Db::name('account')->where('id',$userid)->update($vip_data);
                    //记录日志
                    $log_data=[
                        'log_userid'=>$userid,
                        'log_orderid'=>$out_trade_no,
                        'log_mail'=>session('mail'),
                        'log_text'=>'新开'.$viptime."天".$vinfo['vip_name']."，消费".$vipprice,
                        'log_time'=>time(),
                    ];
                    $add_log=Db::name('viplog')->insert($log_data);
                }else{
                    //查询到期时间执行续费,转为int类型
                    $vendtime=(int)$u['vip_endtime'];
                    //获取续费后的到期时间
                    $endt=$vendtime+$viptime*24*60*60;
                    $vip_data=[
                        'is_vip'=>0,
                        'vip_type'  =>$vid,
                        'vip_endtime'   =>$endt,
                        'day_count'   =>$uservipcountmodel,
                    ];
                    //修改数据库信息
                    $xfvip=Db::name('account')->where('id',$userid)->update($vip_data);
                    //记录日志
                    $log_data=[
                        'log_userid'=>$userid,
                        'log_orderid'=>$out_trade_no,
                        'log_mail'=>session('mail'),
                        'log_text'=>'续费'.$viptime."天".$vinfo['vip_name']."，消费".$vipprice,
                        'log_time'=>time(),
                    ];
                    $add_log=Db::name('viplog')->insert($log_data);
                }
                //找到订单修改状态
                $order_status['order_ispay'] = 1;
                $setorder=Db::name('order')->where("order_id",$out_trade_no)->update($order_status);
                $result_msg['status']=102;
                $result_msg['msg']='支付成功';
                $result_msg['url']='';
                echo json_encode($result_msg,true);die;
                die;
            }
        } else {
            $result_msg['status']=104;
            $result_msg['msg']='未支付';
            $result_msg['url']='';
            echo json_encode($result,true);die;
        }
    }
    //易支付回调函数
    public function epaynotify()
    {
        require_once("vendor/epay/epay_notify.class.php");
        
        $out_trade_no = $_GET['out_trade_no'];
        //先查询订单
        $order_d=Db::name('order')->where('order_id',$out_trade_no)->find();
        $userid=(int)$order_d['order_creat_user'];
        $u_d=Db::name('account')->where('id',$userid)->find();
        $usermail=$u_d['email'];
        if (empty($order_d)) {
           $this->error("订单不存在");
        }
        $paytype=$order_d['order_paytype'];
        $paycon=Db::name('paytype')->where('pay_bs',$paytype)->find();
        //获取支付ID
        $epay['partner']=$paycon['pay_appid'];
        //获取支付秘钥
        $epay['key']=$paycon['pay_private_key'];
        //获取支付网站
        $epay['apiurl']=$paycon['pay_public_key'];
        //签名方式
        $epay['sign_type']    = strtoupper('MD5');
        //编码
        $epay['input_charset']= strtolower('utf-8');
        //判断http还是https
        $epay['transport']= $this->request->scheme();
        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($epay);
        $verify_result = $alipayNotify->verifyNotify();
        if($verify_result) {//验证成功
            if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
                $order=Db::name('order')->where("order_id",$out_trade_no)->where('order_ispay',0)->find();
                if (empty($order)) {
                    echo "fail";die;
                }
                //查询开通的vip
                //vid是会员方式
                $vid=(int)$order['order_goods_id'];
                $vinfo=Db::name('viptype')->where('id',$vid)->find();
                //拿到会员标识
                $vbs=$vinfo['vip_bs'];
                //拿到会员时长
                $viptime=$vinfo['vip_day'];//时长
                //拿到会员价格
                $vipprice=$vinfo['vip_price'];//留着写日志
                //查询当前vip等级是多少次数
                $uservipcountmodel = Db::name('config')->where('dsp_name', $vbs)->value('dsp_value');
                //先查询用户是否开通过vip
                $u=Db::name('account')->where('id',$userid)->find();
                if ($u['is_vip']==1) {
                    //新开通
                    //修改次数，先查询次数
                    $vip_data=[
                        'is_vip'=>0,
                        'vip_type'  =>$vid,
                        'vip_begintime' =>time(),
                        'vip_endtime'   =>time()+$viptime*24*60*60,
                        'day_count'   =>$uservipcountmodel,
                    ];
                    $ktvip=Db::name('account')->where('id',$userid)->update($vip_data);
                    //记录日志
                    $log_data=[
                        'log_userid'=>$userid,
                        'log_orderid'=>$out_trade_no,
                        'log_mail'=>$usermail,
                        'log_text'=>'新开'.$viptime."天".$vinfo['vip_name']."，消费".$vipprice,
                        'log_time'=>time(),
                    ];
                    $add_log=Db::name('viplog')->insert($log_data);
                }else{
                    //查询到期时间执行续费,转为int类型
                    $vendtime=(int)$u['vip_endtime'];
                    //获取续费后的到期时间
                    $endt=$vendtime+$viptime*24*60*60;
                    $vip_data=[
                        'is_vip'=>0,
                        'vip_type'  =>$vid,
                        'vip_endtime'   =>$endt,
                        'day_count'   =>$uservipcountmodel,
                    ];
                    //修改数据库信息
                    $xfvip=Db::name('account')->where('id',$userid)->update($vip_data);
                    //记录日志
                    $log_data=[
                        'log_userid'=>$userid,
                        'log_orderid'=>$out_trade_no,
                        'log_mail'=>$usermail,
                        'log_text'=>'续费'.$viptime."天".$vinfo['vip_name']."，消费".$vipprice,
                        'log_time'=>time(),
                    ];
                    $add_log=Db::name('viplog')->insert($log_data);
                }
                //找到订单修改状态
                $order_status['order_ispay'] = 1;
                $setorder=Db::name('order')->where("order_id",$out_trade_no)->update($order_status);
                $result_msg['status']=102;
                $result_msg['msg']='支付成功';
                $result_msg['url']='';
                //echo json_encode($result_msg,true);die;
               
            }
            echo "success";     //请不要修改或删除
        }else{
            echo "fail";
        }
    }
    //卡密支付
    public function kmpay()
    {
        $km=remove_xss(input('post.kami'));
        if (empty($km)) {
            $this->error("卡密不能为空");
        }
        $kd=Db::name('kami')->where('kami',$km)->find();
        if (empty($kd)) {
            $this->error("卡密不存在");
        }
        if ($kd['useuser']!=0) {
            $this->error("该卡密已被使用");
        }
        if ($kd['usetime']!=0) {
            $this->error("该卡密已被使用");
        }

        $vinfo=Db::name('viptype')->where('id',$kd['vtype'])->find();
        $vid=$vinfo['id'];
        //拿到会员标识
        $vbs=$vinfo['vip_bs'];
        //拿到会员时长
        $viptime=$vinfo['vip_day'];//时长
        //拿到会员价格
        $vipprice=$vinfo['vip_price'];//留着写日志
        //查询当前vip等级是多少次数
        $uservipcountmodel = Db::name('config')->where('dsp_name', $vbs)->value('dsp_value');
        //先查询用户是否开通过vip
        $userid=session('userid');
        $u=Db::name('account')->where('id',$userid)->find();
        if ($u['is_vip']==1) {
            //新开通
            //修改次数，先查询次数
            $vip_data=[
                'is_vip'=>0,
                'vip_type'  =>$vid,
                'vip_begintime' =>time(),
                'vip_endtime'   =>time()+$viptime*24*60*60,
                'day_count'   =>$uservipcountmodel,
            ];
            $ktvip=Db::name('account')->where('id',$userid)->update($vip_data);
                    //记录日志
            $log_data=[
                'log_userid'=>$userid,
                'log_orderid'=>$km,
                'log_mail'=>session('mail'),
                'log_text'=>'新开'.$viptime."天".$vinfo['vip_name']."，消费".$vipprice,
                'log_time'=>time(),
            ];
            $add_log=Db::name('viplog')->insert($log_data);
        }else{
            //查询到期时间执行续费,转为int类型
            $vendtime=(int)$u['vip_endtime'];
            //获取续费后的到期时间
            $endt=$vendtime+$viptime*24*60*60;
            $vip_data=[
                'is_vip'=>0,
                'vip_type'  =>$vid,
                'vip_endtime'   =>$endt,
                'day_count'   =>$uservipcountmodel,
            ];
            //修改数据库信息
            $xfvip=Db::name('account')->where('id',$userid)->update($vip_data);
            //记录日志
            $log_data=[
                'log_userid'=>$userid,
                'log_orderid'=>$km,
                'log_mail'=>session('mail'),
                'log_text'=>'续费'.$viptime."天".$vinfo['vip_name']."，消费".$vipprice,
                'log_time'=>time(),
            ];
            $add_log=Db::name('viplog')->insert($log_data);
        }
        $kdata['useuser']=$userid;
        $kdata['usetime']=time();
        $re=Db::name('kami')->where('kami',$km)->update($kdata);
        if ($re) {
            $this->success("开通成功");
        }else{
            $this->error("开通失败");
        }
    }

}