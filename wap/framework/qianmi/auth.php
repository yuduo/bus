<?php
    $url = "http://oauth.qianmi.com/token";
    $client_id = "10001075";
    $appSecret = "606f2dcLuDO4KbYedikRJXzVg0IsMRa5";
    $grant_type = "authorization_code";
    $code = "f1eefcc3814bcc59dd61c1c565167c6f";
    $data = Array (
           'client_id'  => $client_id,
           'code' => $code,
           'grant_type'  => $grant_type
    );
    ksort($data);
    $plain_text="";
    foreach($data as  $key => $value) {
        $plain_text .= $key.$value;
    }
    $plain_text  = $appSecret.$plain_text.$appSecret;
    $sign = strtoupper(sha1($plain_text));
    $data['sign'] = $sign;
    ksort($data);
    $url_params = "";
	foreach ($data as $key => $value) {
		$url_params .= "&".$key."=".$value;
	}
	$url_params = ltrim($url_params,"&");
	//curl初始化
	$ch = curl_init();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $url_params );
	$return = curl_exec ( $ch );
	//出错检测
	if(curl_errno($ch)){
		echo "curl error:".curl_errno($ch);
	}else{
		print_r($return);
	}
	curl_close ( $ch );
?>