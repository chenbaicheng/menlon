<?php

//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'ViewModel'.DIRECTORY_SEPARATOR.'viewModelBaseClass.php'; //class Tool


class userInformationTitleViewModel extends  viewModelBaseClass{

	public function __construct($dbName){    
	    $parent='viewModelBaseClass';
      
		return $parent::__construct($dbName);
	}
	
	function getUserTitle(&$pdo,$userId){
		try{
			$idArray= Tool::slice_userId_ReturnArray($userId);
			//根据userId 从用户注册表中取出信息,以后要用redis替换   
			$datas =$pdo->select(UserInformation.$idArray[0],[
			'Msg_count',
			'Fans_count',
			'Follow_count',
			'Ualais',
			'UimageSrc',
			'Uinfo'
			],[
			'User_id'=>$idArray[1]
			]);
			$datas2=[];
			if(empty($idArray)){
				throw new PDOException(['code'=>801,'error_msg'=>'没有此用户']);
			}else{
				foreach($datas as $data){
					$datas2['Ualais']=$data['Ualais'];
					$datas2['UimageSrc']=$data['UimageSrc'];
					$datas2['Msg_count']=$data['Msg_count'];
					$datas2['Fans_count']=$data['Fans_count'];
					$datas2['Follow_count']=$data['Follow_count'];
					$datas2['Uinfo']=$data['Uinfo'];
					
				}
				return $datas2; //返回数组 
			}
		}catch(PDOException $exception){
			return $exception;
		}
		
	}
}

?>