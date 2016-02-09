<?php
//这里不写入与数据库有关的代码
//***** 
//***** 
//*****
//***************
//在controller 包含了数据库访问层   
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'ViewModel'.DIRECTORY_SEPARATOR.'viewModelBaseClass.php';

class FavourListViewModel extends  viewModelBaseClass{

	public function __construct($dbName){    
	    $parent='viewModelBaseClass';   
		
		return $parent::__construct($dbName);   
	}

	
	static function getFavourList(&$pdo,$userId,$page=1){  //通过  viewModel 传入view
		// 接受一个对象数组   然后数组序列化生成json
		try{
			$datas = $pdo->select(Favour_const.$userId,
			'*',[
			'LIMIT'=>[GetFavourListCount*$page-GetFavourListCount,GetFavourListCount*$page],
			'ORDER'=>['commitTime DESC']
			]
			);
			
			if(empty($datas)){
				throw new PDOException('empty');
			}
			return $datas;
		}catch(PDOException $exception){
			if($exception->getMessage()=='empty'){
				return ['code'=>802,'error_msg'=>'查询结果为空'];
			}
			
			Tool::debug_content( $exception->getMessage() );
			return '';
		}
		
		
	}
	
}


?>