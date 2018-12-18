<?php
//处理输入的函数文件

//批量转义
function deepslashes($data){
	//判断$data的表现形式,并且需要处理空的情况
	if (empty($data)){
		return $data;
	}
	//中高级程序员的写法
	return is_array($data) ? array_map('deepslashes', $data) : addslashes($data) ;
	/*
	//初级程序员的写法
	if (is_array($data)){
		//数组,对其进行遍历
		foreach ($data as $v){
			return deepslashes($v);
		}
	} else {
		//单一变量
		return addslashes($data);
	}
	*/
}

//批量实体转义
function deepspecialchars($data){
	if (empty($data)){
		return $data;
	}
	return is_array($data) ? array_map('deepspecialchars', $data) : htmlspecialchars($data);
}

function f1(){
	echo "f1 helper...";
}
  //生成唯一的订单号
  function build_order_no(){
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }