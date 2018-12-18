<?php

namespace ENCRYPTER;

class EnctypterCls{

	public static function __encryptValues($algo = "sha512", $value = NULL, $secret = NULL, $func){
	
		if($func === 'hmac_hashing'){
			try{
				$hash_hmac_encrypted_value = hash_hmac($algo, $value, $secret);
				return $hash_hmac_encrypted_value;
			}catch(Exception $e) {
			    return $e->getMessage();
			}
		}elseif($func === 'sha_hashing'){
			try{
				$hash_sha1 = sha1($value);
				return $hash_sha1;
			}catch(Exception $e){
				return $e->getMessage();
			}
		}elseif($func === 'md5_hashing'){
			try{
				$md5_hasher = md5($value);
				return $md5_hasher;
			}catch(Exception $e){
				return $e->getMessage();
			}
		}elseif($func === 'base64_encoding'){
			try{
				$base64_encoded = base64_encode($value);
				return $base64_encoded;
			}catch(Exception $e){
				return $e->getMessage();
			}
		}elseif($func === 'custom_md5_base64_enc'){
			try{
				$custom_value_base64_md5 = md5(base64_encode($value));
				return $custom_value_base64_md5;
			}catch(Exception $e){
				return $e->getMessage();
			}
		}elseif($func === 'custom_sha1_base64_enc'){
			try{
				$custom_value_base64_sha1 = sha1(base64_encode($value));
				return $custom_value_base64_sha1;
			}catch(Exception $e){
				return $e->getMessage();
			}
		}elseif($func === 'custom_hmac_base64_enc'){
			try{
				$custom_value_base64_hmac = hash_hmac($algo, base64_encode($value), base64_encode($secret));
				return $custom_value_base64_hmac;
			}catch(Exception $e){
				return $e->getMessage();
			}
		}
		
	}
	
}

