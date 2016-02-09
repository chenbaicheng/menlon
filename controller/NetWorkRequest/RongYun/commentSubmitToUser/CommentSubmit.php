<?php

//***** 
//***** 
//*****
//引入融云  
require_once Tool::returnAbsoluteRouteString(['controller','server-sdk-php','ServerAPI.php']);

require_once Tool::returnAbsoluteRouteString(['view','Json_encode_decode.php']);

require_once Tool::returnAbsoluteRouteString(['controller','NetWorkRequest','RongYun','MissionListForLocalDatabase.php']);//用本地文件代替数据库做 队列 
class CommentSubmit {
	//这里负责将评论的推送内容格式化
	static  function commentContentFormat($totalArray){
	
		$totalArray['objectName']='RC:CmdNtf';
		$totalArray['timestamp']=time()+900;
		return JSON_Array_Control::array_to_json(['name'=>'comment','data'=>$totalArray] );
	}

	static function commentSubmitToRongYun($fromUserId,$toUserId,$dataArray){
		/*
$dataArray 应该包含下面的
replyId -->这个不用
userId	-->用来定位评论
messageId -->用来定位评论
ForUalais	-->这里是指发出评论的人 可以是楼主 所有用户  
ForUserId	-->userId
AtUserId	-->他回复的人,如果没有  默认就是楼主  
AtUserName	-->回复的Ualais  ,如果没有默认是楼主昵称 
dependReplyId  -->忽略这个 
replyContent  -->回复内容 
replyDateTime  -->这个可以要  
		*/
		$message_static_file = Tool::returnAbsoluteRouteString(['controller','NetWorkRequest','RongYun','cacheForLocalSubmitToDataBase.json']);
		$serverObj = new ServerAPI(RONG_YUN_APPKEY,RONG_YUN_APPSECRET);
		$jsonString =CommentSubmit::commentContentFormat($dataArray);
		$resultJsonString = $serverObj->messagePublish($fromUserId,$toUserId,'RC:CmdNtf',$jsonString);
			//将json 转换为数组,然后返回 
		$conditionCheckArray = JSON_Array_Control::JsonString_to_array($resultJsonString);
		
		if( /* 0 */ $conditionCheckArray['code']==200){
			
			return true ;
		}else if( /* 1  */ $conditionCheckArray['code']==429||$conditionCheckArray['code']==500||$conditionCheckArray['code']==504){
			MissionListForLocalDatabase::addToMissonList($message_static_file,JSON_Array_Control::JsonString_to_array($jsonString));
			return true;
		}else { //未知错误 
			
			return $conditionCheckArray;
		}
		
		
		
	}
	
	
	

}


?>