<?php

//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'ViewModel'.DIRECTORY_SEPARATOR.'viewModelBaseClass.php'; //class Tool

//用户索引中间件  
require_once Tool::returnAbsoluteRouteString(['controller','ViewModel','UserManager','UserInformationManager','UalaisIndex','UalaisIndex.php']);

class FindManagerViewModule extends  viewModelBaseClass{

	public function __construct($dbName){    
	    $parent='viewModelBaseClass';
		
		return $parent::__construct($dbName);
	}
	
	//话题搜索 搜索话题总列表 --不用静态化文件,但是需要静态化函数  
	static function findTitleByTitleName(&$pdo,$titleName,$page=1){
		try{
			$datas =$pdo->select(MessageTitle,[
			'Tid',
			'Tname',
			'TmessageCount',
			'TsubmitTime',
			'Tcontent'
			],[
			'Tname[~]'=>$titleName.'%',
			'ORDER'=>['TmessageCount DESC'],
			'LIMIT'=>[FindManagerModule_TitleCOUNT*$page-FindManagerModule_TitleCOUNT,FindManagerModule_TitleCOUNT*$page]
			]);
			if(empty($datas)){
				throw new PDOException('empty');
			}
			
			return $datas;
		

		}catch(PDOException $exception){
			if($exception->getMessage()=='empty'){
				return ['code'=>802,'error_msg'=>'在此页码下表中无数据'];
			}
			//其他错误 就记录在文本里面
			Tool::debug_content( $exception->getMessage() );
			return '';
		}
	}
	
	//学校搜索 搜索学校总列表 --不用静态化,学校不分页  因为数量不是很多 不适合分页 
	static function findSchoolBySchoolName(&$pdo,$schoolName){
		try{
			$datas = $pdo->select(SCHOOLTITLE,[
			'schoolId',
			'schoolName',
			'schoolMessageCount',
			'submitTime',
			'content'
			],[
			'schoolName[~]'=>$schoolName.'%',
			'ORDER'=>['schoolMessageCount DESC']
			]);
			if(empty($datas)){
				throw new PDOException('empty');
			}
			//echo $pdo->last_query();
			return $datas;
		}catch(PDOException $exception){
			if($exception->getMessage()=='empty'){
				return ['code'=>802,'error_msg'=>'在表中无此数据'];
			}
			//其他错误 就记录在文本里面
			Tool::debug_content( $exception->getMessage() );
			return '';
		}
		
	}
	
	//用户搜索 ,先建立10张用户昵称哈希表(取模分表)
	static function findUserByUalais(&$pdo,$Ualais,$page){
		$id = UalaisIndex::getHashTableIdByUalais($Ualais);
		try{
			$datas =$pdo->select($id.UalaisIndexTableName,[
			'Ualais',
			'userId',
			'UimageSrc',
			'Uinfo'
			],[
			'Ualais[~]'=>$Ualais.'%',
			'LIMIT'=>[FindManagerModule_UserCOUNT*$page-FindManagerModule_UserCOUNT,FindManagerModule_UserCOUNT*$page]
			]);
			if(empty($datas)){
				throw new PDOException('empty');
			}
			return $datas;
		}catch(PDOException $exception){
			if($exception->getMessage()=='empty'){
				return ['code'=>802,'error_msg'=>'在表中无此数据,或者继续输入名称'];
			}
			if(strrpos($exception->getMessage(),'doesn\'t exist') ){
				return ['code'=>802,'error_msg'=>'请继续输入名称,或表中无此数据'];
			}
			//其他错误 就记录在文本里面
			
			Tool::debug_content( $exception->getMessage() );
			return '';
		}
	}
	
}

?>