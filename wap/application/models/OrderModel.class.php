<?php
//线路模型
class OrderModel extends Model{
   //查询目标线路订单
   public function getOrders($line_sale_id,$date){
   	$sql="select sum(count) from line_order where line_sale_id = '$line_sale_id' and date ='$date'";
   	return $this->db->getOne($sql);
   }	
   //生成订单
   public function createOrder($data){
    $id=$this->insert($data);
    return $id;
   }
   //判断是否有订单
   public function  getInfo($line_sale_id,$accToken, $date){
    $sql="select * from line_order where line_sale_id = '$line_sale_id' and accToken ='$accToken' and $date = '$date'";
    return $this->db->getrow($sql);
   }
  //查询我的订单
  public function getOrderByUserInfo($accToken){
     $sql="select * from line_order where accToken ='$accToken'";
    return $this->db->getAll($sql);
  }
  //列表
   public function getOrderList($accToken){
    $sql="select a.*,b.*,c.* from line_order as a join line_sale as b on a.line_sale_id = b.id join line_time as c on c.id = b.line_time_id where a.accToken = '$accToken'";
   return $this->db->getAll($sql);

  }
  //根据订单号查找订单
  public function getOrderByOrderNum($orderNum){
       $sql="select * from line_order where orderNum = '$orderNum'";
	   $row = $this->db->getrow($sql);
	   
	   if($row)
	   if($row['status']==0)
	   {
		   $current=date('Y-m-d h:i:s',time()+8*60*60);
		   
		  if( round(strtotime($current)-strtotime($row['create_time']))> 10*60)
		  {
			  
			  
			  $updateSql="update line_order set status=-1 where orderNum = '$orderNum'";
			  $this->db->query($updateSql);
		  }else
		  {
			  return $row;
		  }
	   }
	   
    return $this->db->getrow($sql);
  }
    //根据订单号 查询 线路详情 a->order  b->time_detail c->line_time
   public function getDetaiByOrderNum($orderNum){
      $sql="select a.*,b.*,c.* from line_order as a join line_sale as b on a.line_sale_id = b.id join line_time as c on c.id = b.line_time_id where a.orderNum = '$orderNum'";
      return $this->db->getrow($sql);
   }
}