<?php
//用户模型
class AlipayModel extends Model{
   //insert
   public function insertOrder($total_fee,$trade_no,$seller_id,$subject,$seller_email,$out_trade_no,$notify_time,$notify_id,$buyer_id,$buyer_email,$body){
	  
      $sql='INSERT INTO alipay_order (total_fee,trade_no,seller_id,subject,seller_email,out_trade_no,notify_time,notify_id,buyer_id,buyer_email,body) VALUES (\''.$total_fee.'\',\''.$trade_no.'\',\''.$seller_id.'\',\''.$subjec.'\',\''.$seller_email.'\',\''.$out_trade_no.'\',\''.$notify_time.'\',\''.$notify_id.'\',\''.$buyer_id.'\',\''.$buyer_email.'\',\''.$body.'\')';
			echo $sql;
      return $this->db->query($sql);
   }
  
}

