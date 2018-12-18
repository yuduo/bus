<?php
//线路模型
class LineInfoModel extends Model{
   //通过名字查询线路
   public function getLine($startCityName,$endCityName){
   	$sql="select * from line_city where start = '$startCityName' and stop = '$endCityName'";
   	return $this->db->getRow($sql);
   }
    //通过id查询线路
   public function getLineById($id){
   	$sql="select * from line_city where id = $id ";
   	return $this->db->getRow($sql);
   }
	
}