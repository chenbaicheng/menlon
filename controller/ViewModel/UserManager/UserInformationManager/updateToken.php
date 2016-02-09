<?php
//
//***** 
//***** 
//*****
//***************
require_once Tool::returnAbsoluteRouteString(['controller','server-sdk-php','ServerAPI.php']);

class updateToken {

	// 这里使用   
	static function getToken(&$pdo,$userId,$Ualais,$imgurl='null'){
			//检查userid是否存在,使用  $this->checkUserIdIsCorrect();
			$checkResult = updateToken::checkUserIdIsCorrect($pdo,$userId);
			if(!$checkResult){//数据库错误检查 
				//不存在 
				return false;
			}
			$p = new ServerAPI(RONG_YUN_APPKEY,RONG_YUN_APPSECRET);
			$r = $p->getToken($userId,$Ualais,$imgurl);
			$arrayFromJson = JSON_Array_Control::JsonString_to_array($r);
			if($arrayFromJson['code']==200){ //这个是融云的错误提示
				return $arrayFromJson; //返回token
			}else{
				//对于200以外  全部返回错误提示  这里就不借用view层直接输出 
				return false;
			}

	}
	
	static function updateTokenToDatabase(&$pdo,$userId,$token){
		try{
			
			$idArray = Tool::slice_userId_ReturnArray($userId);
			$updateRow = $pdo->update(UserInformation.$idArray[0],[
				'uuid'=>$token
				],[
				'User_id'=>$idArray[1]
				]);
			return true;
		}catch(PDOException $exception){
			return false;
		}
	}
	
	static function checkUserIdIsCorrect(&$pdo,$userId){
		try{
			try{
				$userId_slice_array =explode('_',$userId);

			}catch(Exception $exception){
				return false;
			}
			

			$datas = $pdo->select(UserInformation.$userId_slice_array[0],[
			'User_id'
			],[
			'User_id'=>$userId_slice_array[1] /*这是用户所在行数*/
			]);
			//如果表不存在 会直接抛出错误,如果已经存在,就会返回结果值  
			if(empty($datas)){ //为空 就是不存在,返回错误 
				return false;
			}else{
				return true;
			}
		}catch(PDOException $exception){
			
			Tool::debug_content( $exception->getMessage());
			return false;
		}
		
		return false; //一般在try 就会结束 ,所以如果执行到这里,就是出错了  会返回false 
	}

}

?>