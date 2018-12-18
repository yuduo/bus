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
			
			$jsResourcePath="http://localhost/web/wxclient/v3.0";
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
  
   //chat
  public function chatAction(){
		$basePath = BASE_PATH;
		
		
		include CUR_VIEW_PATH . "chat.html";
		
		
	}
	
	//chat
  public function helpAction(){
		$basePath = BASE_PATH;
		$type=$_GET['type'];
		if($type==1)
		{
			$helpContent='<div class="main-wrap">
<div class="main_center"><div class="help-right">
	
	<div class="pa30">
		<!--详情-->
		<div class="comprod_b">
			<div class="det_title">
				<p class="f16">
					<a href="">网上订票支付是否安全？</a>
				</p>
			</div>
			<div class="det_content">
				<p>当您开始进行网上支付时，您的操作就从12307网站转移到了新打开的支付页面中进行（请不要关闭网站的浏览器窗口，否则将会人为造成支付差错），这个时候您的网上支付的安全性和技术可靠性完全由银行或第三方支付平台来提供和保证，具体说明可以参考银行或第三方支付平台关于网上支付的说明。</p>
			</div>
		</div>
		<div class="h30 clearfix"></div>
		
	</div>
</div>';
		}else if($type==2)
		{
			$helpContent='<div class="main-wrap">
<div class="main_center"><div class="help-right">
	
	<div class="pa30">
		<!--详情-->
		<div class="comprod_b">
			<div class="det_title">
				<p class="f16">
					<a href="">预订并提交订单后必须要立即支付吗？</a>
				</p>
			</div>
			<div class="det_content">
				<p>提交订单后请您在系统提示时间内完成支付，一旦支付超时，订单会自动取消，需要您重新预订下单噢。</p>
			</div>
		</div>
		<div class="h30 clearfix"></div>
		
	</div>
</div>';
		}else if($type==3)
		{
			$helpContent='<div class="main-wrap">
<div class="main_center"><div class="help-right">
	
	<div class="pa30">
		<!--详情-->
		<div class="comprod_b">
			<div class="det_title">
				<p class="f16">
					<a href="">为什么订单支付完成后页面还是显示正在付款？</a>
				</p>
			</div>
			<div class="det_content">
				<p>这种情况一般是由于数据返回有延误，您可以登录用户中心后台查看订单状态，如果发现订单状态仍然显示为待付款，您可以联系客服人员解决问题。</p>
			</div>
		</div>
		<div class="h30 clearfix"></div>
		
	</div>
</div>';
		}else if($type==4)
		{
			$helpContent='<div class="main-wrap">
<div class="main_center"><div class="help-right">
	
	<div class="pa30">
		<!--详情-->
		<div class="comprod_b">
			<div class="det_title">
				<p class="f16">
					<a href="">购票成功后没有收到短信怎么办？</a>
				</p>
			</div>
			<div class="det_content">
				<p>这种情况一般是由于短信返回有延误，您可以登录用户中心后台查看订单状态，如果发现订单状态是支付成功,就可以放心了。</p>
			</div>
		</div>
		<div class="h30 clearfix"></div>
		
	</div>
</div>';
		}else if($type==5)
		{
			$helpContent='<div class="main-wrap">
<div class="main_center"><div class="help-right">
	
	<div class="pa30">
		<!--详情-->
		<div class="comprod_b">
			<div class="det_title">
				<p class="f16">
					<a href="">一般可以提前几天购票?</a>
				</p>
			</div>
			<div class="det_content">
				<p>现在是3天以内。</p>
			</div>
		</div>
		<div class="h30 clearfix"></div>
		
	</div>
</div>';
		}else if($type==6)
		{
			$helpContent='<div class="main-wrap">
<div class="main_center"><div class="help-right">
	
	<div class="pa30">
		<!--详情-->
		<div class="comprod_b">
			<div class="det_title">
				<p class="f16">
					<a href="">如何成为12307会员</a>
				</p>
			</div>
			<div class="det_content">
				<p>通过手机注册成为12307会员。</p>
			</div>
		</div>
		<div class="h30 clearfix"></div>
		
	</div>
</div>';
		}else if($type==7)
		{
			$helpContent='<div class="main-wrap">
<div class="main_center"><div class="help-right">
	
	<div class="pa30">
		<!--详情-->
		<div class="comprod_b">
			<div class="det_title">
				<p class="f16">
					<a href="">如何联系我们？</a>
				</p>
			</div>
			<div class="det_content">
				<p>通过电话,微信,QQ,在线咨询联系我们。</p>
			</div>
		</div>
		<div class="h30 clearfix"></div>
		
	</div>
</div>';
		}else if($type==8)
		{
			$helpContent='<div class="main-wrap">
<div class="main_center"><div class="help-right">
	
	<div class="pa30">
		<!--详情-->
		<div class="comprod_b">
			<div class="det_title">
				<p class="f16">
					<a href="">如何查询预订汽车票</a>
				</p>
			</div>
			<div class="det_content">
				<p>可以订单预订页面查询搜索。</p>
			</div>
		</div>
		<div class="h30 clearfix"></div>
		
	</div>
</div>';
		}else if($type==9)
		{
			$helpContent='<div class="main-wrap">
<div class="main_center"><div class="help-right">
	
	<div class="pa30">
		<!--详情-->
		<div class="comprod_b">
			<div class="det_title">
				<p class="f16">
					<a href="">如何购票下订单</a>
				</p>
			</div>
			<div class="det_content">
				<p>查询订单后,需要登陆页面,下单后需要及时支付,否则订单会被取消。</p>
			</div>
		</div>
		<div class="h30 clearfix"></div>
		
	</div>
</div>';
		}else if($type==10)
		{
			$helpContent='<div class="main-wrap">
<div class="main_center"><div class="help-right">
	
	<div class="pa30">
		<!--详情-->
		<div class="comprod_b">
			<div class="det_title">
				<p class="f16">
					<a href="">如何网上支付</a>
				</p>
			</div>
			<div class="det_content">
				<p>目前已开通网银、支付宝、财付通、微信支付等多种支付方式哟。</p>
			</div>
		</div>
		<div class="h30 clearfix"></div>
		
	</div>
</div>';
		}else if($type==11)
		{
			$helpContent='<div class="main-wrap">
<div class="main_center"><div class="help-right">
	
	<div class="pa30">
		<!--详情-->
		<div class="comprod_b">
			<div class="det_title">
				<p class="f16">
					<a href="">如何退票，退款？</a>
				</p>
			</div>
			<div class="det_content">
				<p>12307会在3个工作日内自动办理原路退款，到账时间视银行或第三方支付平台规定，约1-10个工作日到账。</p>
			</div>
		</div>
		<div class="h30 clearfix"></div>
		
	</div>
</div>';
		}
		include CUR_VIEW_PATH . "help.html";
		
		
	}
	
}