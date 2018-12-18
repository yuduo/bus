<?php
//首页控制器
header("Content-type:text/html;charset=utf-8");
 require QM_PATH."OpenSdk.php";
class IndexController extends Controller{
	
	
	//载入首页面
	public function indexAction(){
		
		if(!isset($_REQUEST['startCityInput']))
		  {
			
			  $startCityInput =  '输入您出发的城市';
				$endCityInput =  '输入您要去的城市';
		  }else
		  {
			 $startCityInput =  urldecode(filter_var($_REQUEST['startCityInput'],FILTER_SANITIZE_STRING));
			 $endCityInput =  urldecode(filter_var($_REQUEST['endCityInput'],FILTER_SANITIZE_STRING));
	 
		 }
		$basePath = BASE_PATH;
		
		include CUR_VIEW_PATH . "index.html";
	}
	  //查找终点线路
	  public function searchEndCityAction(){
		//起点城市
		$startCityName =  urldecode(filter_var($_REQUEST['startCityName'],FILTER_SANITIZE_STRING));
		$filename = PUBLIC_PATH.'searchEndCity'.'/'.$startCityName.".js";
		//$filename = iconv('UTF-8','GB2312',$filename);
		$fp = fopen($filename, "r"); 
	   if($fp) 
	   { 
		$content = fread($fp, 88000); 
		echo $content;
	   } 
	   else 
	   { 
	   echo ""; 
	   } 
	   fclose($fp);
	
	  }
	  function searchAllLine($startCityName,$endCityName,$date)
	  {
		   //查找线路
		 $weekday = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六'); 
		$lineInfo= new LineInfoModel('line_city');
		$line=$lineInfo->getLine($startCityName,$endCityName);
		if($line)
		{
			//如果有线路,查询时刻表
			$line_time=new LineTimeModel('line_time');
			$times=$line_time->getTimes($line['id']);
			$res=(($line_time->getQMTimes($startCityName,$endCityName)));
			
			
			
			$times_info=array();
			
			//判断是否有票,无票不显示
				
				foreach ($times as $k => $v) {
					$lastOrder=$this->getRemainOrder($v['scheduleCode'],$date);
					if($lastOrder>0){
					   $v['has_ticket']=1;
					}else{
					  $v['has_ticket']=0;
					 }
					$v['is_sale'] =1;
					array_push($times_info,$v);
				} 
				
			if(isset($res))
			{
				
			
				$json=json_decode($res);
			
				$coachLines=$json->{'coach_lines_list_response'}->{'coachLines'}->{'coachLine'};
				
				if( is_array($coachLines))
				{
					
					foreach ($coachLines as $line){ 
						$single=array(  
						  'scheduleCode' => "QM".$line->{'coachNO'},  
						  'start_place' => $line->{'dptStation'},
						  'stop_place'=>$line->{'arrStation'}, 
						  'has_ticket'=>$line->{'ticketLeft'}=="0"?0:1,
						  'time'=>$line->{'dptTime'}, 
							
						  'price' => $line->{'ticketPrice'}  
						); 
						array_push($times_info,$single);
						
					}
				}
			}
			
			 
			
			
				
				
				
				include CUR_VIEW_PATH . "line.html";
			
		}else{
			include CUR_VIEW_PATH . "scheduleCode/noline.html";
		}
	   }
	//查询线路
	public function searchLineAction(){
		$basePath = BASE_PATH;
		//接受数据
		$startCityName =  urldecode(filter_var($_REQUEST['startCityName'],FILTER_SANITIZE_STRING));
	  $endCityName =  urldecode(filter_var($_REQUEST['endCityName'],FILTER_SANITIZE_STRING));
	  if(isset($_REQUEST['date']))
	  $date=urldecode(filter_var($_REQUEST['date'],FILTER_SANITIZE_STRING));
	  else
	  $date=date("Y-m-d", strtotime("tomorrow"));
	  
	  if($startCityName=='上海' || $startCityName=='苏州' || $startCityName=='杭州' || $startCityName=='无锡')
	  {
		  $this->searchAllLine($startCityName,$endCityName,$date);
		  return;
	   }
		 //查找线路
		 $weekday = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六'); 
		$lineInfo= new LineInfoModel('line_city');
		$line=$lineInfo->getLine($startCityName,$endCityName);
		if($line)
		{
			//如果有线路,查询时刻表
			$line_time=new LineTimeModel('line_time');
			$times=$line_time->getTimes($line['id']);
			if(!$times){
				$this->searchLineFromQM($startCityName,$endCityName,$date);
			}else{
				//判断是否有票,无票不显示
				$times_info=array();
				foreach ($times as $k => $v) {
					$lastOrder=$this->getRemainOrder($v['scheduleCode'],$date);
					if($lastOrder>0){
					   $v['has_ticket']=1;
					}else{
					  $v['has_ticket']=0;
					 }
					$times_info[]=$v;
				} 
				
				$v['is_sale'] =1;
				include CUR_VIEW_PATH . "line.html";
			}
		}else{
			$this->searchLineFromQM($startCityName,$endCityName,$date);
		}
	}
	function getAccessToken()
	{
		$get_local_token = file_get_contents('/var/www/html/token/log/get_token.txt');
        $token_array = json_decode($get_local_token,true);
		
 		return $token_array['access_token'];
	}
	function searchLineFromQM($startCityName,$endCityName,$date)
	{
		 $loader  = new QmLoader;
		$loader  -> autoload_path  = array(CURRENT_FILE_DIR.DS."client");
		$loader  -> init();
		$loader  -> autoload();
				 $client  = new OpenClient;
		$client  -> appKey =  "10001075";
		$client  -> appSecret =  "606f2dcLuDO4KbYedikRJXzVg0IsMRa5";
		$accessToken  = $this->getAccessToken();
		 $req = new CoachLinesListRequest;
		$req->setFrom($startCityName);
		$req->setTo($endCityName);
		$req->setDate($date);
		$res = $client->execute($req, $accessToken);
		
		$_SESSION['return_json']=$res;
		 $weekday = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六');
		if(isset($res))
		{
			
			 $coachLines=$res->{'coachLines'}->{'coachLine'};
			 if( !is_array($coachLines))
			 {
				 $times_info=array();
				 $single=array(  
				  'scheduleCode' => "",  
				  'start_place' => $startCityName,
				  'stop_place'=>$endCityName, 
				  'has_ticket'=>0,
				  'time'=>"", 
				    
				  'price' => ""  
				); 
			 	array_push($times_info,$single);
				 $basePath = BASE_PATH;
				 include CUR_VIEW_PATH . "line.html";
				 return;
			 }
			 $times_info=array();
			 foreach ($coachLines as $line){ 
			 	$single=array(  
				  'scheduleCode' => "QM".$line->{'coachNO'},  
				  'start_place' => $line->{'dptStation'},
				  'stop_place'=>$line->{'arrStation'}, 
				  'has_ticket'=>$line->{'ticketLeft'}=="0"?0:1,
				  'time'=>$line->{'dptTime'}, 
				    
				  'price' => $line->{'ticketPrice'}  
				); 
			 	array_push($times_info,$single);
      			 
    		}
			
			 
			 
			 //return;
			$basePath = BASE_PATH;
			include CUR_VIEW_PATH . "line.html";
			
		}else{
			include CUR_VIEW_PATH . "scheduleCode/noline.html";
		}
	}
	function lineInfoFromQM($scheduleCode,$startCityName,$endCityName)
	{
		
		
		
		if(isset($_SESSION['return_json']))
		{
			$return_json=$_SESSION['return_json'];
			$coachLines=$return_json->{'coachLines'}->{'coachLine'};
			
			
		}else
		{
			//
			$line_time=new LineTimeModel('line_time');
			$res=(($line_time->getQMTimesFromCode(trim($scheduleCode,'QM'),$startCityName,$endCityName)));
			//echo trim($scheduleCode,'QM'),$startCityName,$endCityName;
			//echo $res;
			if(isset($res))
			{
				
			
				$json=json_decode($res);
			//echo $json;
				$coachLines=$json->{'coach_lines_list_response'}->{'coachLines'}->{'coachLine'};
				
				
			}
		}
		foreach ($coachLines as $sigle){
				if("QM".$sigle->{'coachNO'}==$scheduleCode)
				{
					$line['start']=$sigle->{'departure'};
					$line['stop']=$sigle->{'destination'};
					$times['start_place']=$sigle->{'dptStation'};
					$times['stop_place']=$sigle->{'arrStation'};
					$times['price']=$sigle->{'ticketPrice'};
					$times['time']=$sigle->{'dptTime'};
					$date=$sigle->{'dptDate'};
					$times['ticket']=intval($sigle->{'ticketLeft'});
					$times['scheduleCode']=$scheduleCode;
					break;
				}
			}
		$times['is_sale']=0;
			$driver_info['name']='';
			$driver_info['phone']='';
			$driver_info['plate_number']='';
			$driver_info['company']='';
			$weekday = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六'); 
				$basePath = BASE_PATH;
			  include CUR_VIEW_PATH . "scheduleCode/line_infos.html";
	}
  //线路详情
  public function lineInfosAction(){
  	//判断是否还有票
    $scheduleCode=$_GET['scheduleCode'];
	$startCityName=$_GET['startCityName'];
	$endCityName=$_GET['endCityName'];
    $date=$_GET['date'];
	if(strstr($scheduleCode,"QM"))
	{
		$this->lineInfoFromQM($scheduleCode,$startCityName,$endCityName);
		return;
	}
    $lastOrder=$this->getRemainOrder($scheduleCode,$date);
    if($lastOrder>0){
    $line_time=new LineTimeModel('line_time');
    $times=$line_time->getTime($scheduleCode);
    $line_info=new LineInfoModel('line_city');
    $line=$line_info->getLineById($times['line_city_id']);
  	if($times){
    //查询线路司机信息
    $driver=new DriverModel('line_driver_info');
    $driver_info=$driver->getDriverInfo($times['driver_id']);
    $weekday = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六'); 
	$basePath = BASE_PATH;
	$times['is_sale']=1;
	  include CUR_VIEW_PATH . "scheduleCode/line_infos.html";
    }else{
    	include CUR_VIEW_PATH . "noline.html";
    }
  }else{
     echo '没票了';
  }
  }
  //判断线路有没有票
  public function checkIsBookingAction(){
     $scheduleCode=$_REQUEST['scheduleCode'];
     $date=$_REQUEST['date'];
	 if(strstr($scheduleCode,"QM"))
	{
		
		echo '{"ctx":"","respTime":"'.date('y-m-d h:i:s',time()).'","rsCode":"0","rsDesc":"有余坐","lastOrder":1}';
		return;
	}
     $lastOrder=$this->getRemainOrder($scheduleCode,$date);
     if($lastOrder>0){
     echo '{"ctx":"","respTime":"'.date('y-m-d h:i:s',time()).'","rsCode":"0","rsDesc":"有余坐","lastOrder":'.$lastOrder.'}';
     }else{
     echo '{"ctx":"","respTime":"'.date('y-m-d h:i:s',time()).'","rsCode":"1","rsDesc":"无余坐"}';
   }
  }
  //订票
  public function orderAction(){
	
	
		 $scheduleCode=$_REQUEST['scheduleCode'];
		 if(strstr($scheduleCode,"QM"))
			{
				//登陆用户显示
				 if(isset($_SESSION["accToken"])){
					 $user_detail=new UserModel('members');
					$passengerJson=$user_detail->getPassenger($_SESSION["accToken"]);
					$accToken=$_SESSION["accToken"];
					$detail['start']=$_REQUEST['startCityName'];
					$detail['stop']=$_REQUEST['endCityName'];
					$detail['time']=$_REQUEST['time'];
					$detail['start_place']=$_REQUEST['start_place'];
					$detail['stop_place']=$_REQUEST['stop_place'];
					$detail['price']=$_REQUEST['price'];
					$basePath = BASE_PATH;
					 include CUR_VIEW_PATH . "busbooking.html";
				 }
				 else
				{
					$backURL='/index.php?p=home&c=index&a=order&scheduleCode='.$scheduleCode.'&date='.$date.'&startCityName='.$_REQUEST['startCityName'].'&endCityName='.$_REQUEST['endCityName'].'&time='.$_REQUEST['time'].'&start_place='.$_REQUEST['start_place'].'&stop_place='.$_REQUEST['stop_place'].'&price='.$_REQUEST['price'];
					$basePath=BASE_PATH;
					$jsResourcePath="http://m.12307.net/wxclient/v3.0";
					include CUR_VIEW_PATH . "fastLogin.html";
				}
				return;
			}
		 $starttime=$_REQUEST['starttime'];
		 $date=$_REQUEST['date'];
		 //根据code 找到线路详情
		 $line_sale=new LineTimeModel('line_time');
		 $weekday = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六'); 
		 $detail=$line_sale->getDetai($scheduleCode);
		 //$detail['time']=date('H:i',$detail['time']);
		 //余票查询
		 $lastOrder=$_REQUEST['lastOrder'];
		 //登陆用户显示
		 if(isset($_SESSION["accToken"])){
			 $user_detail=new UserModel('members');
		 	$passengerJson=$user_detail->getPassenger($_SESSION["accToken"]);
			$accToken=$_SESSION["accToken"];
			$basePath = BASE_PATH;
			 include CUR_VIEW_PATH . "busbooking.html";
		 }
		 else
		{
			$backURL='/index.php?p=home&c=index&a=lineInfos&scheduleCode='.$scheduleCode.'&date='.$date;
			$basePath=BASE_PATH;
			$jsResourcePath="http://localhost/wap/wxclient/v3.0";
			include CUR_VIEW_PATH . "fastLogin.html";
		}
		 /*else
		 {
			 
			 $passengerJson='{"object":[{"certificateNo":"321283198809144888","certificateType":"01","defaultOne":0,"mobilePhone":"13776100957","pageNum":-1,"pageSize":0,"passengerId":2583717,"passengerName":"叶红","startRecord":0,"totalCount":0,"userId":1489769}],"resultCode":"0000","resultMsg":"操作成功","scheduleStatus":0,"totalCount":0}';
		 }*/
		
	
	
     
  }
  //确认订单
  public function confirmOrderAction(){
    //检查是否登陆
    // if(!$login){
    //    echo '{"resultCode":"-1010","resultMsg":"bus1605029204079","scheduleStatus":0,"totalCount":0}';
    //    exit;
    // }
    //检验数据合法性
    $line_time=new LineTimeModel('line_time');
    $line_sale_id=$line_time->getIdByCode($_POST['scheduleCode']);
    if(!$line_sale_id){
      echo '非法操作';
    }
    //查询单价
    $price=$line_time->getPrice($line_sale_id);
      // 载入辅助函数
	
    $date=date('Y-m-d',time());
    $this->helper('input');
    $data['line_sale_id']=$line_sale_id;
    $data['username']=deepspecialchars($_POST['name']);
    $data['phone']=deepspecialchars($_POST['phone']);
	$data['certificateNo']=deepspecialchars($_POST['certificateNo']);
	$data['startPlace']=deepspecialchars($_POST['startPlace']);
	$data['endPlace']=deepspecialchars($_POST['endPlace']);
	$data['start']=deepspecialchars($_POST['startCity']);
	$data['stop']=deepspecialchars($_POST['stopCity']);
	$data['starttime']=deepspecialchars($_POST['starttime']);
    $data['accToken']=$_POST['accToken'];//暂时
    $data['count']=$_POST['count'];
    $data['date']=deepspecialchars($_POST['date']);//$date;//;strtotime($date);
	$data['create_time']=date('Y-m-d h:i:s',time()+8*60*60);
    // $data['time']=date('H:i:s',time());
    $data['status']=0;
    //价格。。。。//根据优惠规则确认
    $data['price']=$_POST['count']*$price;
    //判断是否重复提交
    $order=new OrderModel('line_order');
    $info=$order->getInfo($line_sale_id,$_POST['accToken'],$data['date']);
    if($info){
		  //已有订单
		 echo '{"resultCode":"-3","resultMsg":"订单已存在,去支付","scheduleStatus":0,"totalCount":0,"orderNum":'.$info['orderNum'].'}';
		 exit;
    }else{
		 // 生成订单，到数据库
		 $data['orderNum']=build_order_no();//唯一订单号
		 $res=$order->createOrder($data);
    if($res){
    	echo '{"resultCode":"0000","resultMsg":"'.$data['orderNum'].'","scheduleStatus":0,"totalCount":0,"orderNum":'.$data['orderNum'].'}';
    }else{
    	echo '{"resultCode":"1111","resultMsg":"'.$data['orderNum'].'","scheduleStatus":0,"totalCount":0}';
    }
    }
      
  }
  //生成订单
  public function orderNoAction(){
    $orderNum=$_REQUEST['orderNum'];
    //查找订单详情
    $order=new OrderModel('line_order');
    $orderinfo=$order->getDetaiByOrderNum($orderNum);
    if($orderinfo){
    $weekday = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六'); 
	$basePath = BASE_PATH;
    include CUR_VIEW_PATH . "chepiaoPay.html";
    }else{
      echo '没有订单信息！';
    }
  }
  //我的订单
  public function myOrderAction(){
     //查询我的订单
	 //$orderNum=$_REQUEST['orderNum'];
	 /*if($orderNum)
	 {
		 
	 }else*/
	 {
		//  $order=new OrderModel('line_order');
		// $accToken=session('accToken');
		//$accToken='i2P/m/HF3g0=';
		//$myorderinfo=$order->getOrderByUserInfo($accToken);
		//var_dump($myorderinfo);
		$basePath = BASE_PATH;
		include CUR_VIEW_PATH . "order/myOrder.html";
	  }
   
  }
  //查询余票
  public function getRemainOrder($scheduleCode,$date){
     //1.查询总票
     $lineTime=new LineTimeModel('line_time');
     $ticket=$lineTime->getTicket($scheduleCode);
     $line_sale_id=$lineTime->getIdByCode($scheduleCode);
    // 2.查询已定的票
      $date=strtotime($date);
     $order=new OrderModel('line_order');
     $userOrders=$order->getOrders($line_sale_id,$date);//预定的数量
     //3.余票
     $lastOrder=(int)$ticket-(int)$userOrders;
     return $lastOrder;
  }
  
  //weixin login
  public function wxAction()
  {
	  $appId='wx248cbb7ef43dfac2';
	  $reqParam='12307';
	  $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appId."&redirect_uri=".urlencode("http://m.12307.net/index.php?p=home&c=index&a=wxAuthorize")."&response_type=code&scope=snsapi_base&state=".$reqParam."#wechat_redirect";
	  header('Location: '.$url);
   }
   //授权
   public function wxAuthorizeAction()
   {
	   $appid = "wx248cbb7ef43dfac2"; 
		$secret = "1e00dc3a9cf1f5755a8f341bf17882c2"; 
		$code = $_GET["code"]; 
		$get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$get_token_url); 
		curl_setopt($ch,CURLOPT_HEADER,0); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); 
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
		$res = curl_exec($ch); 
		curl_close($ch); 
		$json_obj = json_decode($res,true); 
		//根据openid和access_token查询用户信息 
		$access_token = $json_obj['access_token']; 
		$openid = $json_obj['openid']; 
		//$login_model=new LoginModel('members');
		//$login_model->insertUser('18061995226',$openid);
		$_SESSION['accToken'] =$openid;
		$this->indexAction();
		/*$get_user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN'; 
		
		$ch = curl_init(); 
		curl_setopt($ch,CURLOPT_URL,$get_user_info_url); 
		curl_setopt($ch,CURLOPT_HEADER,0); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); 
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
		$res = curl_exec($ch); 
		curl_close($ch); 
		
		//解析json 
		$user_obj = json_decode($res,true); 
		$_SESSION['user'] = $user_obj; 
		print_r($user_obj); */
	}
	//下载
   public function wxDownloadAction()
   {
	   
	}
	//Help
   public function wxHelpAction()
   {
	   include CUR_VIEW_PATH . "wenti.html";
	}
}