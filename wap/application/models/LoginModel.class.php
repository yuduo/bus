<?php

//登陆模型
class LoginModel extends Model{
   
   public function getLogin($mobilePhone){
      $sql='SELECT * FROM members where mobilePhone=\''.$mobilePhone.'\'';
      return $this->db->getrow($sql);
   }
   
   public function insertUser($mobilePhone,$accToken){
	  
      $sql='INSERT INTO members (accToken,mobilePhone) VALUES (\''.$accToken.'\',\''.$mobilePhone.'\')';
      return $this->db->query($sql);
   }
}