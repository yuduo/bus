<?php
//用户模型
class UserModel extends Model{
   //查询用户
   public function getPassenger($token){
   	$sql="select * from passenger where memberID = '$token'";
	$list = $this->db->getAll($sql);
	if(isset($list))
	{
		$arrList=array();
		foreach ($list as $user)
		{
			$arr=array("certificateNo"=>$user['certificateNo'],
			"certificateType"=>"01","defaultOne"=>0,
			"pageNum"=>-1,"pageSize"=>0,
			"passengerId"=>$user['id'],"passengerName"=>$user['passengerName'],
			"mobilePhone"=>$user['mobilePhone'],
			"userId"=>$user['id']
			);
			 array_push($arrList,$arr);
		}
		return '{"object":'.json_encode($arrList).',"resultCode":"0000","resultMsg":"操作成功","scheduleStatus":0,"totalCount":'.count($arrList).'}';
	}
	return '{"resultCode":"0000","resultMsg":"操作成功","scheduleStatus":0,"totalCount":0}';
	/*return '{"object":[{"certificateNo":"","certificateType":"01","defaultOne":0,"mobilePhone":"'.$user['mobilePhone'].'","pageNum":-1,"pageSize":0,"passengerId":0,"passengerName":"","startRecord":0,"totalCount":0,"userId":'.$user['id'].'}],"resultCode":"0000","resultMsg":"操作成功","scheduleStatus":0,"totalCount":0}';*/
	
   }
  //插入用户
   public function addOrUpdatePassenger($add_passengerId,$add_passengerName,$add_certificateNo,$add_mobilePhone){
   	$sql="insert into passenger(memberID,passengerName,certificateNo,mobilePhone) values('$add_passengerId','$add_passengerName','$add_certificateNo','$add_mobilePhone')";
	$user = $this->db->query($sql);
	
   }
}