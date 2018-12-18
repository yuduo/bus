<?php

//首页控制器
header("Content-type:text/html;charset=utf-8");
class PayController extends Controller{

	
	  //微信
	  public function wxpayOrderAction(){
		  
		  $pack ="prepay_id=wx201605030936473a19318c670841845698";

		$prepayId ="wx201605030936473a19318c670841845698";
	
		$timeStamp ="1462239407365";
	
		$nonceStr ="0f6c4aca25ce40e3a4e05ee191bff6e6";
	
		$signType ="MD5";
	
		$appId ="wxc9847938780ba8d1";
	
		$sign ="7AFD775661219592FAD68E267109C505";
	
		$orderId ="23794";
	
		$orderNo ="bus1605033080518";
		
		$orderList = "http://wap.12307.com/wxpay/callBack.html?orderId=23794&orderNo=bus1605033080518";
	
		$basePath = BASE_PATH;
		include CUR_VIEW_PATH . "wxpayOrder.html";
	
	  }
	  //wap
	  public function wapPayOrderAction(){
		  
		  $pack ="prepay_id=wx201605030936473a19318c670841845698";

			$prepayId ="wx201605030936473a19318c670841845698";
		
			$timeStamp ="1462239407365";
		
			$nonceStr ="0f6c4aca25ce40e3a4e05ee191bff6e6";
		
			$signType ="MD5";
		
			$appId ="wxc9847938780ba8d1";
		
			$sign ="7AFD775661219592FAD68E267109C505";
		
			$orderId ="23794";
		
			$orderNo ="bus1605033080518";
		  $orderList = "http://wap.12307.com/wxpay/callBack.html?orderId=23794&orderNo=bus1605033080518";
		  
		  $basePath = BASE_PATH;
		include CUR_VIEW_PATH . "wapPayOrder.html";
	
	  }
	//支付宝
	public function alipayPayOrderAction(){
	  
	  //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = $_REQUEST['orderNo'];

		 //查找订单详情
   	 	$order=new OrderModel('line_order');
    	$orderinfo=$order->getDetaiByOrderNum($out_trade_no);
	
        //订单名称，必填
        $subject = $orderinfo['start_place']."到".$orderinfo['stop_place'];

        //付款金额，必填
        $total_fee = $orderinfo['price'];
		//$total_fee =0.01;
		
        //商品描述，可空
        //$body = "即时到账测试";
		$body=$orderinfo['orderNum'].$orderinfo['start_place'].$orderinfo['stop_place'].$orderinfo['price'];
		//echo $body;
    	include AL_PATH."alipayapi.php";

  }
  
  //back
  public function returnUrlAction(){
	  require_once(AL_PATH."alipay.config.php");
	require_once(AL_PATH."lib/alipay_notify.class.php");


	//计算得出通知验证结果
	$alipayNotify = new AlipayNotify($alipay_config);
	$verify_result = $alipayNotify->verifyReturn();
	$verify_result=1;
	if($verify_result) {//验证成功
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//请在这里加上商户的业务逻辑程序代码
		
		//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
		//获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
	
		//商户订单号
	
		$out_trade_no = $_GET['out_trade_no'];
	
		//支付宝交易号
	
		$trade_no = $_GET['trade_no'];
	
		//交易状态
		$trade_status = $_GET['trade_status'];
	
	
		if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
			//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
			$total_fee = $_GET['total_fee'];
			
			$trade_no = $_GET['trade_no'];
			$seller_id = $_GET['seller_id'];
			
			$subject = $_GET['subject'];
			$seller_email = $_GET['seller_email'];
			$out_trade_no = $_GET['out_trade_no'];    
			$notify_time = $_GET['notify_time'];
			$notify_id = $_GET['notify_id'];
			$buyer_id = $_GET['buyer_id'];
			$buyer_email = $_GET['buyer_email'];
			//echo "验证成功<br />";
			$body = $_GET['body'];
			
			include CUR_VIEW_PATH . "paySuccess.html";
			
			$alipay=new AlipayModel('alipay_order');
			$alipay->insertOrder($total_fee,$trade_no,$seller_id,$subject,$seller_email,$out_trade_no,$notify_time,$notify_id,$buyer_id,$buyer_email,$body);
			
			
		}
		else {
		  echo "trade_status=".$_GET['trade_status'];
		}
			
			/*echo $out_trade_no."<br />";
			echo $trade_no."<br />";
			echo $trade_status."<br />";
			
		echo "验证成功<br />";*/
	
		//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	}
	else {
		//验证失败
		//如要调试，请看alipay_notify.php页面的verifyReturn函数
		echo "验证失败";
	}

  }
  
  //nitify
  public function notifyUrlAction(){
	  
	  require_once(AL_PATH."alipay.config.php");
		require_once(AL_PATH."lib/alipay_notify.class.php");
		
		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		
		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代
		
			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			
			//获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
			
			//商户订单号
		
			$out_trade_no = $_POST['out_trade_no'];
		
			//支付宝交易号
		
			$trade_no = $_POST['trade_no'];
		
			//交易状态
			$trade_status = $_POST['trade_status'];
		
		
			if($_POST['trade_status'] == 'TRADE_FINISHED') {
				//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
					//如果有做过处理，不执行商户的业务程序
						
				//注意：
				//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
		
				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
			}
			else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
					//如果有做过处理，不执行商户的业务程序
						
				//注意：
				//付款完成后，支付宝系统发送该交易状态通知
		
				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
				$total_fee = $_GET['total_fee'];
			
				$trade_no = $_GET['trade_no'];
				$seller_id = $_GET['seller_id'];
				
				$subject = $_GET['subject'];
				$seller_email = $_GET['seller_email'];
				$out_trade_no = $_GET['out_trade_no'];    
				$notify_time = $_GET['notify_time'];
				$notify_id = $_GET['notify_id'];
				$buyer_id = $_GET['buyer_id'];
				$buyer_email = $_GET['buyer_email'];
				//echo "验证成功<br />";
				$body = $_GET['body'];
				$alipay=new AlipayModel('alipay_order');
				$alipay->insertOrder($total_fee,$trade_no,$seller_id,$subject,$seller_email,$out_trade_no,$notify_time,$notify_id,$buyer_id,$buyer_email,$body);
			
				
			}
		
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
				
			echo "success";		//请不要修改或删除
			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		else {
			//验证失败
			echo "fail";
		
			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}
  }
}