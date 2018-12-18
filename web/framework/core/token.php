<?php
function set_token() {
	$_SESSION['token'] = md5(microtime(true));
}
function valid_token() {
	$return = $_REQUEST['token'] === $_SESSION['token'] ? true : false;
	set_token();
	return $return;
}

/*// 如果token为空则生成一个token
if (! isset($_SESSION['token']) || $_SESSION['token'] == ''){
	set_token();
}

if (isset($_POST['test'])){
	if (! valid_token()){
		echo "token error";
	}else{
		echo '成功提交，Value:' . $_POST['test'];
	}
}*/

/**
 * 表单令牌(防止表单恶意提交)
 */
class RequestToken {
	// session_start();
	const SESSION_KEY = 'SESSION_KEY_wangqian_aiche';
	/**
	 * 生成一个当前的token
	 *
	 * @param string $form_name        	
	 * @return string
	 */
	public static function getToken($form_name) {
		$key = self::granteKey();
		$_SESSION['TOKEN.$form_name'] = $key;
		$token = md5(substr(time(), 0, 3) . $key . $form_name);
		return $token;
	}
	
	/**
	 * 验证一个当前的token
	 *
	 * @param string $form_name        	
	 * @return string
	 */
	public static function isToken($form_name, $token) {
		$key = $_SESSION['TOKEN.$form_name'];
		
		$old_token = md5(substr(time(), 0, 3) . $key . $form_name);
		if ($old_token == $token){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 删除一个token
	 *
	 * @param string $form_name        	
	 * @return boolean
	 */
	public static function dropToken($form_name) {
		unset($session[SESSION_KEY]);
		return true;
	}
	
	/**
	 * 生成一个密钥
	 *
	 * @return string
	 */
	public static function granteKey() {
		$encrypt_key = md5(((float)date("YmdHis") + rand(100, 999)) . rand(1000, 9999));
		return $encrypt_key;
	}
	
	/**
	 * 能解析的令牌
	 */
	
	public static function base64UrlDecode($input) {
		return base64_decode(strtr($input, '-_', '+/'));
	}
	public static function base64UrlEncode($input) {
		return base64_encode(strtr($input, '+/', '-_'));
	}
	
	
	// 生成令牌
	public static function parseSignedRequest($signed_request, $secret = '4f5fcdc6514f7ee25ec4fa7c7853e8e1') {
		list($encoded_sig, $payload) = explode('.', $signed_request, 2);
		
		// decode the data
		$sig = base64UrlDecode($encoded_sig);
		$data = json_decode(self::base64UrlDecode($payload), true);
		
		if (strtoupper($data['algorithm']) !== 'HMAC-SHA256'){
			die('Unknown algorithm. Expected HMAC-SHA256');
			return null;
		}
		
		// check sig
		$expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
		if ($sig !== $expected_sig){
			die('Bad Signed JSON signature!');
			return null;
		}
		
		return $data;
	}
	
	// 解析
	public static function generateSignature($info, $secret = '4f5fcdc6514f7ee25ec4fa7c7853e8e1') {
		$body = self::base64UrlEncode(json_encode(($info)));
		$sign = hash_hmac('sha256', $body, $secret, true);
		
		$signed_request = self::base64UrlEncode($sign) . "." . $body;
		
		return $signed_request;
	}
}

?>