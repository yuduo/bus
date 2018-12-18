<?php
//线路模型
class LineTimeModel extends Model{
   //查询线路
   public function getTimes($id){
   	$sql="select a.*,b.*,c.* from line_city as a join line_time as b on a.id = b.line_city_id  join line_sale as c on c.line_time_id = b.id where a.id = $id ";
   	return $this->db->getAll($sql);
   }
   //查询单价
   public function getPrice($id){
         $sql="select price from line_sale  where id = '$id'";
      return $this->db->getone($sql);
   }
   //查询时刻线路
   public function getTime($scheduleCode){
   	$sql="select a.*,b.* from line_time as a inner join line_sale as b on a.id = b.line_time_id where b.scheduleCode = '$scheduleCode'";
   	return $this->db->getRow($sql);
   }
   //查询时刻线路总票数
   public function getTicket($scheduleCode){
   	$sql="select ticket from line_sale as a  where a.scheduleCode = '$scheduleCode'" ;
   	return $this->db->getOne($sql);
   }
   //查询scheduleCode
   public function getIdByCode($scheduleCode){
   	$sql="select id from line_sale as a  where a.scheduleCode = '$scheduleCode'" ;
   	return $this->db->getOne($sql);
   }
   //根据条件查询线路详情
   public function getDetai($scheduleCode){
   $sql="select a.*,b.*, c.* from line_sale as a join line_time as b on a.line_time_id = b.id join line_city as c on b.line_city_id = c.id where a.scheduleCode = '$scheduleCode' ";
   return $this->db->getRow($sql);
   }
 
	//千米缓存
	public function getQMTimes($startCity,$endCity){
   	$sql="select json from qm_line where startCity= '$startCity' and endCity ='$endCity' ";
   	return ($this->db->getOne($sql));
   }
   public function getQMTimesFromCode($scheduleCode,$startCityName,$endCityName){
   	$sql="select * from qm_schedule where coachNO= '$scheduleCode' and departure='$startCityName' and destination='$endCityName'";
   	return ($this->db->getOne($sql));
   }
   
}