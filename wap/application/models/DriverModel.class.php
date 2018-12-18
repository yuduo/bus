<?php
//线路模型
class DriverModel extends Model{
   //查询线路
   public function getDriverInfo($id){
   	$sql="select * from line_driver_info where id = $id";
   	return $this->db->getRow($sql);
   }
  
}