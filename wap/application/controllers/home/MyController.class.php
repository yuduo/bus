<?php

//首页控制器
header("Content-type:text/html;charset=utf-8");
class MyController extends Controller{

	//载入首页面
	public function indexAction(){
		$basePath = BASE_PATH;
		
		if( isset($_SESSION['accToken']))
		{
			
			$accToken=$_SESSION['accToken'];
			include CUR_VIEW_PATH . "order/myOrder.html";
		}else
		{
			$backURL='';
			
			$jsResourcePath="http://localhost/wap/wxclient/v3.0";
			include CUR_VIEW_PATH . "fastLogin.html";
		}
		
	}
  
  
  public function searchMyOrderAction(){
	    $order=new OrderModel('line_order');
		 $accToken=$_SESSION['accToken'];
		
		$myorderinfo=$order->getOrderList($accToken);
		//var_dump($myorderinfo);
		$basePath = BASE_PATH;
		include CUR_VIEW_PATH . "order/orderList.html";
  }
  
  //detail
  public function orderDetailsAction(){
		$basePath = BASE_PATH;
		$orderNo=$_GET['orderNo'];
		$order=new OrderModel('line_order');
		$orderDetail=$order->getOrderByOrderNum($orderNo);
		$current=date('Y-m-d h:i:s',time()+8*60*60);
		$LeftSecond=floor((strtotime($current)-strtotime($orderDetail['create_time']))%86400/60);
		
		if( $LeftSecond > 10)
		{
			$LeftSecond=-1*$LeftSecond*60*1000;
		}else
		{
			$LeftSecond=$LeftSecond*60*1000;
		}
		include CUR_VIEW_PATH . "OrderDetail.html";
		
		
	}
	
  //chat
  public function chatAction(){
		$basePath = BASE_PATH;
		
		
		include CUR_VIEW_PATH . "chat.html";
		
		
	}
	//aboutus
  public function aboutusAction(){
		$basePath = BASE_PATH;
		
		
		include CUR_VIEW_PATH . "aboutUs.html";
		
		
	}
	
	public function ajaxAddOrUpdatePassengerAction(){
		$add_passengerId=$_REQUEST['accToken'];//$_REQUEST['add_passengerId'];
		 $add_passengerName=$_REQUEST['passengerName'];
		 $add_certificateNo=$_REQUEST['certificateNo'];
		 $add_mobilePhone=$_REQUEST['mobilePhone'];
		 $passenger=new UserModel('passenger');
		 $passenger->addOrUpdatePassenger($add_passengerId,$add_passengerName,$add_certificateNo,$add_mobilePhone);
		 echo '{"resultCode":"0000","resultMsg":"OK" ,"object":[{"certificateNo":"'.$add_certificateNo.'","mobilePhone":"'.$add_mobilePhone.'","passengerId":20,"passengerName":"'.$add_passengerName.'","userId":1489769}]}';
		
	}
}