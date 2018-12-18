<?php
//用户模型
class UserModel extends Model{
   //查询用户
   public function getPassenger($token){
   	$sql="select * from members where accToken = '$token'";
	$user = $this->db->getRow($sql);
	
	return '{"object":[{"certificateNo":"","certificateType":"01","defaultOne":0,"mobilePhone":"'.$user['mobilePhone'].'","pageNum":-1,"pageSize":0,"passengerId":0,"passengerName":"","startRecord":0,"totalCount":0,"userId":'.$user['id'].'}],"resultCode":"0000","resultMsg":"操作成功","scheduleStatus":0,"totalCount":0}';
	
   }
  
}