<?php
require_once "sendCheckMsg.php";
require_once "EnctypterCls.class.php";
require_once 'framework/core/token.php';
//首页控制器
header("Content-type:text/html;charset=utf-8");
class LoginController extends Controller{

	//载入首页面
	public function indexAction(){
		$basePath = BASE_PATH;
		include CUR_VIEW_PATH . "index.html";
	}
  //登陆
  public function ajaxFastLoginAction(){
	  
    $step = filter_var($_REQUEST['step'],FILTER_SANITIZE_STRING);
	if($step != 'one')
	{
		$mobilePhone = filter_var($_REQUEST['mobilePhone'],FILTER_SANITIZE_STRING);
		$password = rand(10,100);
		$password = \ENCRYPTER\EnctypterCls::__encryptValues("sha512", $password, "", "custom_md5_base64_enc");
		$validateCode = filter_var($_REQUEST['validateCode'],FILTER_SANITIZE_STRING);
		if($validateCode != $_SESSION['codes'])
		{
			echo '验证码错误';	
			return;
		}
		$login_model=new LoginModel('members');
    	$login_info=$login_model->getLogin($mobilePhone);
	
		
			if($login_info['id']){
				
				$_SESSION['accToken'] =$login_info['accToken'];
				echo 'sucess';
			}else
			{
				$accToken = RequestToken::generateSignature($mobilePhone);
				//insert
				$re=$login_model->insertUser($mobilePhone,$accToken);
				if($re)
				{
					$_SESSION['accToken'] = $accToken;
					
					echo 'sucess';
				}else{
					echo 'error';	
				}	
				
			}
		
		
	}else
	{
		$mobilePhone = filter_var($_REQUEST['mobilePhone'],FILTER_SANITIZE_STRING);
	
		$codes = rand(1000,9999);
		$ret=sendCheckMsg($codes,$mobilePhone);
	
		$ret_json = json_decode($ret);
		
		if("000000" != $ret_json->resp->respCode)
		{
			echo '验证码发送失败';
			return;
		}
		$_SESSION['mobilePhone']=$mobilePhone;
		$_SESSION['codes']=$codes;
		echo 'sucess';
	}

  }
	
}