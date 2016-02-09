<?php

//***** 
//***** 
//*****
//不能单独使用  需要在融云的推送处理使用


class MissionList {
	//添加进消息队列 
	static  function addToMissonList(&$pdo,$insertArray){
		try{//传入的有 fromUserId,toUserId ,content
		//下面是系统添加
			$insertArray['timestamp']=time();
			$insertArray['uuid']=Tool::returnUUID($insertArray['timestamp']);//这里插入uuid是为了更新时方便识别哪一行 
			$conditionCheck =$pdo->insert(MISSIONLIST,$insertArray);
			return true;
		}catch(PDOException $exception){
			Tool::debug_content( $exception->getMessage() );
			return false;
		}
	}
	//根据页数 取出待发发送的消息 ,每页1000 
	static function getMissionList(&$pdo){
		try{
			$datas =$pdo->select(MISSIONLIST,[
			'timestamp',/*用来更新哪一行的 标识位*/
			'fromUserId',
			'toUserId',
			'content'
			],[
			'type'=>0,
			'ORDER'=>['timestamp ASC'],
			'LIMIT'=>[0,GETMISSSIONLISTCOUNT]//现在默认是1000 具体看db.config.php
			]);
			return $datas;
		}catch(PDOException $exception){
			Tool::debug_content( $exception->getMessage() );
			return false;
		}
	}
	
	static function updateMissionList(&$pdo,$uuidArray){
		try{
			$conditionCheck =$pdo->update(MISSIONLIST,[
			'type'=>1 //1代表发送成功 
			],[
			'uuid'=>$uuidArray //注意这里使用缓冲更新,每发送完一条,就在数组添加uuid 直到全部发送完成,在update
			]);
			//如果没有更新一行 不用抛出错误,即使一条没有更新,也不是危及系统的错误
			return true;
		}catch(PDOException $exception){
			Tool::debug_content( $exception->getMessage() );
			return false;
		}
	}
	
	static function deleteMissionList(&$pdo){
		try{
			$conditionCheck =$pdo->delete(MISSIONLIST,[
			'type'=>1
			]);
			//如果没有更新一行 不用抛出错误,即使一条没有更新,也不是危及系统的错误
			return true;
		}catch(PDOException $exception){
			Tool::debug_content( $exception->getMessage() );
			return false;
		}
	}
}


?>