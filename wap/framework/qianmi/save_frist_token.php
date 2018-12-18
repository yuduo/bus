<?php
    define('ROOT',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/");
    
	
    function curl( $url , $postFields = NULL )
    {
        $ch = curl_init();
        curl_setopt( $ch , CURLOPT_TIMEOUT , 3 );
        curl_setopt( $ch , CURLOPT_URL , $url );
        curl_setopt( $ch , CURLOPT_FAILONERROR , FALSE );
        curl_setopt( $ch , CURLOPT_RETURNTRANSFER , TRUE );
        //https 请求
        if ( strlen( $url ) > 5 && strtolower( substr( $url , 0 , 5 ) ) == 'https' ){
            curl_setopt( $ch , CURLOPT_SSL_VERIFYPEER , FALSE );
            curl_setopt( $ch , CURLOPT_SSL_VERIFYHOST , FALSE );
        }
 
        if ( is_array( $postFields ) && 0 < count( $postFields ) ){
            $postBodyString = '';
            $postMultipart  = FALSE;
            foreach ( $postFields as $k => $v ) {
                if ( '@' != substr( $v , 0 , 1 ) ) //判断是不是文件上传
                {
                    $postBodyString .= "$k=" . urlencode( $v ) . "&";
                } else {
                    //文件上传用multipart/form-data，否则用www-form-urlencoded
                    $postMultipart = TRUE;
                }
            }
            $postFields = trim( $postBodyString , '&' );
            unset( $k , $v );
            curl_setopt( $ch , CURLOPT_POST , TRUE );
            if ( $postMultipart ){
                curl_setopt( $ch , CURLOPT_POSTFIELDS , $postFields );
            } else {
                curl_setopt( $ch , CURLOPT_POSTFIELDS , $postFields );
            }
        }
 
        $reponse = curl_exec( $ch );
        curl_close( $ch );
        return $reponse;
    }
    //获取千米token
    //远程获取 千米token
    function curl_get_qianmi_token($refresh_token){
        //去千米获取，然后保存
        $url = "http://oauth.qianmi.com/token";
		$client_id = "10001075";
		$appSecret = "606f2dcLuDO4KbYedikRJXzVg0IsMRa5";
		$grant_type = "authorization_code";
		$code = "1ee6113a6cdfa5e7573807e32be90d51";
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
			echo $return;
			$TOKEN_json = json_decode($return,true);
			echo $TOKEN_json;
			file_put_contents(qianmi_token_file(),json_encode($TOKEN_json['data']));//保存到本地
			return $TOKEN_json;
		}
		curl_close ( $ch );
        
    }
    
 
    function qianmi_token_file(){
        return ROOT.'log/get_token.txt';
    }
 
 
    $now_time = time();
    $token_array = curl_get_qianmi_token($now_time);
    $access_token = $token_array['access_token'];