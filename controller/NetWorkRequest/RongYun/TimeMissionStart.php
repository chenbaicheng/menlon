<?php

//***** 
//***** 
//*****
//不能单独使用  需要在融云的推送处理使用
//引入融云  
//require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'databaseAccessProxy_Model'.DIRECTORY_SEPARATOR.'medoo_returnPDO.php'; //class Tool

require_once  $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'databaseAccessProxy_Model'.DIRECTORY_SEPARATOR.'tool.php';

require_once Tool::returnAbsoluteRouteString(['controller','server-sdk-php','ServerAPI.php']);

require_once Tool::returnAbsoluteRouteString(['view','Json_encode_decode.php']);

require_once 'MissionListForLocalDatabase.php'; //用本地文件代替数据库做 队列 

require_once 'commentSubmitToUser'.DIRECTORY_SEPARATOR.'CommentSubmit.php';
class TimeMissionStart {
	/*
	--从文件中读取数据
	--然后转换会数组
	--利用融云api发送
	--判断返回状态值,是否抛弃这个发送  
	*/
	static private function updateMissionArrayWhenReturn(&$arrayTmp,&$arrayTmpForLocalCache,$message_static_file){
		if(!empty($arrayTmpForLocalCache)){
			//将缓冲的数组 重新写入主数组,然后写入文件 结束这次任务队列
			foreach ($arrayTmpForLocalCache as $data){
				MissionListForLocalDatabase::ArrayUnShift($arrayTmp,$data);//不能直接将 数组集添加到 数组队首,如果直接插入集合 不会一个个分开的插入,需要自己手动插入
			}
			//var_dump($arrayTmp);
		}
			//将空的 或者还没有完成的任务 重新写入文件 
		file_put_contents($message_static_file,JSON_Array_Control::array_to_json($arrayTmp) );
	}
	
	static function commentTimeMission(&$message_static_file,&$serverObj){
		
		//从文件中读取数组 
		//$message_static_file = Tool::returnAbsoluteRouteString(['controller','NetWorkRequest','RongYun','cacheForLocalSubmitToDataBase.json']);
		$arrayTmp=[];
		$arrayTmp2=[];
		$arrayTmpForLocalCache =[];
		$target =0; //锁标志  是否从文件中读入json 1就是加锁  
		for(;;){ 
			
			$array=[];
			//无限循环  知道请求次数超过 限制  ,跳出循环  
			if(empty($arrayTmp)&&$target==0){
				
				//读一次 以后直接在内存取  
				$arrayTmp =  JSON_Array_Control::JsonString_to_array(file_get_contents($message_static_file));//文件读到内存 
				$array = array_shift($arrayTmp);//弹出队首的
			}else{
				
				$array = array_shift($arrayTmp); //继续弹出队首 
			}
			
			//echo '<br /> <br />';
			
			//开始上锁  因为当 $arrayTmp 为空的时候 会再次读取文件,但是那时候 数据还没有写回文本<数据不一致,需要 if(empty($arrayTmp)&&empty($array)) 成立 才说明任务完成 >,所以会发生错误,一定发现$arrayTmp为空 就开始上锁,保证数据正确 
			if(empty($arrayTmp)){
				$target =1;
			}
			if(empty($arrayTmp)&&empty($array)){
				//返回前要写入文件 ,首先要更新缓冲队列 ,插回原数组 
				TimeMissionStart::updateMissionArrayWhenReturn($arrayTmp,$arrayTmpForLocalCache,$message_static_file);
				return true;//队列为空 
			}
			
			
			if(($array['data']['timestamp']-time())<0){
				//说明这个消息已经过期了  删除他(因为shift 后,就删除了队首 所以不处理删除操作),读取下一个 

				continue; //结束本次循环 
			}
			$resultJsonString = $serverObj->messagePublish($array['data']['ForUserId'],[$array['data']['AtUserId']],'RC:CmdNtf',CommentSubmit::commentContentFormat($array));
			//将json 转换为数组,然后返回 
			$conditionCheckArray = JSON_Array_Control::JsonString_to_array($resultJsonString);
			//开始状态检查,如果是参数错误,就返回false,如果是请求频率超过限制,或者融云服务器宕机 ,将信息放入任务队列
			if( $conditionCheckArray['code']==200){
				//成功了  继续下一个  
				continue ;
			}else if( $conditionCheckArray['code']==500||$conditionCheckArray['code']==504){
				//添加入缓冲队列 等处理完 再重新插入数组  
				$arrayTmpForLocalCache[]= $array;
				
				continue ; 
				
			}else if($conditionCheckArray['code']==429){ //超出频率应该结束 
			//当请求频率超出限制后,
				TimeMissionStart::updateMissionArrayWhenReturn($arrayTmp,$arrayTmpForLocalCache,$message_static_file);
				return true;
			}
		} 
		
	}
}

$serverObj = new ServerAPI(RONG_YUN_APPKEY,RONG_YUN_APPSECRET);
//定时执行 
ignore_user_abort(1); // run script in background 
set_time_limit(0); // run script forever 
$interval=60; // do every 15 minutes... 
$message_static_file = Tool::returnAbsoluteRouteString(['controller','NetWorkRequest','RongYun','cacheForLocalSubmitToDataBase.json']); //不要用相对路径,因为主程序入口不在这里(有可能),一般在controller是入口

do{ 
   // add the script that has to be ran every 15 minutes here 
   // ... 
   TimeMissionStart::commentTimeMission($message_static_file,$serverObj);
   sleep($interval); // wait 15 minutes 
}while(true); 
?>