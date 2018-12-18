<?php
require_once "sendCheckMsg.php";

    define('ROOT',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/");
    header("Content-type:text/html;charset=utf-8");
	
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
	
	function post($url, $post_data = '', $timeout = 5){//curl
 
        $ch = curl_init();
 
        curl_setopt ($ch, CURLOPT_URL, $url);
 
        curl_setopt ($ch, CURLOPT_POST, 1);
 
        if($post_data != ''){
 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
 
        }
 
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
 
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
 
        curl_setopt($ch, CURLOPT_HEADER, false);
 
        $file_contents = curl_exec($ch);
 
        curl_close($ch);
 
        return $file_contents;
 
    }
	
    //获取千米token
    //远程获取 千米token
    function curl_get_qianmi_token($refresh_token){
		
		$appSecret = "606f2dcLuDO4KbYedikRJXzVg0IsMRa5";
		
        //去千米获取，然后保存
		/*$post_data = array () ;
		$post_data [ 'client_id' ] ='10001075';
		$post_data [ 'grant_type' ] ='refresh_token';
		$post_data [ 'sign' ] ='SHA1';
		$post_data [ 'refresh_token' ] =$refresh_token;*/
		
		$post_data =array(  
		  'client_id' => '10001075',  
		  'grant_type' => 'refresh_token',
		  'state'=>'1',   
		  'refresh_token' => $refresh_token  
		); 

		ksort($post_data);
		$totalStr='';
		foreach($post_data as $x=>$x_value)
		  {
			  echo "Key=" . $x . ", Value=" . $x_value;
			  echo "<br>";
			  $totalStr=$totalStr.$x.$x_value;
		  } 
		  
		  //echo $totalStr."<br>";
		  $sign= strtoupper(sha1($appSecret.$totalStr.$appSecret));
		$post_data [ 'sign' ] =  $sign;
		 //echo $sign."<br>"; 
		 /*foreach($post_data as $x=>$x_value)
		  {
			  echo "Key=" . $x . ", Value=" . $x_value;
			  echo "<br>";
			  
		  }*/
		//return;
        $TOKEN = send_post('https://oauth.qianmi.com/token', $post_data);  //post('https://oauth.qianmi.com/token',$post_data);
		//echo $TOKEN;
        $TOKEN_json = json_decode($TOKEN);
		$token_array=$TOKEN_json->{'data'};
		if(!isset($token_array->{'access_token'}))
		{
			sendCheckMsg('token获取失败','15601903152');
			//return;
		}
        //$TOKEN_json['get_token_time'] = time();
        file_put_contents(qianmi_token_file(),json_encode($token_array));//保存到本地
        return $TOKEN_json;
    }
	
	/** 
	 * 发送post请求 
	 * @param string $url 请求地址 
	 * @param array $post_data post键值对数据 
	 * @return string 
	 */  
	function send_post($url, $post_data) {  
	  
	  $postdata = http_build_query($post_data);  
	  $options = array(  
		'http' => array(  
		  'method' => 'POST',  
		  'header' => 'Content-type:application/x-www-form-urlencoded',  
		  'content' => $postdata,  
		  'timeout' => 15 * 60 // 超时时间（单位:s）  
		)  
	  );  
	  $context = stream_context_create($options);  
	  $result = file_get_contents($url, false, $context);  
	  
	  return $result;  
	} 


    //本地获取 千米token（如果不成功或者超时，就去远程获取）
    function file_get_qianmi_token($now_time){
        //去千米获取，然后保存
        $get_local_token = file_get_contents(qianmi_token_file());
        $token_array = json_decode($get_local_token,true);
 
        //判断本地的qianmi_token是否存在
        
            //去千米获取，然后保存
            $token_array = curl_get_qianmi_token($token_array['refresh_token']);
        
        
        return $token_array;
    }
 
    function qianmi_token_file(){
        return ROOT.'log/get_token.txt';
    }
 

    $now_time = time();
    file_get_qianmi_token($now_time);
    //$access_token = $token_array['access_token'];