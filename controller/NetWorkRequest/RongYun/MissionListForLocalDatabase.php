<?php

//***** 
//***** 
//*****
//不能单独使用  需要在融云的推送处理使用


class MissionListForLocalDatabase {
	
	//添加进消息队列 ,文件 
	static  function addToMissonList($message_static_file,$insertArray){
		//$message_static_file=Tool::returnAbsoluteRouteString(['controller','NetWorkRequest','RongYun','cacheForLocalSubmitToDataBase.json']);
		if(file_exists($message_static_file)){
			//从文件中读取对象
			$jsonArray = JSON_Array_Control::JsonString_to_array(file_get_contents($message_static_file));
			//插入对象 注意这里已经是多维数组了
			$jsonArray[]=$insertArray;
			//重新放入文件 
			file_put_contents($message_static_file,JSON_Array_Control::array_to_json($jsonArray) );

			return true;

		}else{
			//创建文件 
			$multipleArrayFormatTmp[]=$insertArray;
			file_put_contents($message_static_file,JSON_Array_Control::array_to_json($multipleArrayFormatTmp) );

			return true;
		}
	}
	//根据文件名 取出所有记录
	static function getMissionList($message_static_file){
		//$message_static_file=Tool::returnAbsoluteRouteString(['controller','NetWorkRequest','RongYun','cacheForLocalSubmitToDataBase.json']);
		try{
	
			$jsonString = file_get_contents($message_static_file);
			$array =JSON_Array_Control::JsonString_to_array($jsonString);
			return array_shift($array);//弹出队首的 
		}catch(Exception $exception){
			Tool::debug_content( $exception->getMessage() );
			return false;
		}
	}
	
	//这里删除数组,插入出队首的 
	static function ArrayUnShift(&$array,$insertArray){
		array_unshift($array,$insertArray);
	}
}


?>