<?php

//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'databaseAccessProxy_Model'.DIRECTORY_SEPARATOR.'medoo_returnPDO.php'; //class Tool

class viewModelBaseClass extends medoo_returnPDO {

	public function __construct($dbName){

		//检查token是否存在,如果没有直接exit()中止代码
		$token;
		$userId;

		
		
		
		//$parent = 'medoo_returnPDO';
		$pdo = parent::__construct($dbName);//这里才开始初始化对象,如果在前面初始完再判断有点消耗资源
		
		//调试模式关闭拦截器
		if(!STACK_CONDITION){
			//什么都不做 调试模式
		}else{
			if( isset($_GET['hashKey'])&&isset($GET['userId']) ){
				$token =$_GET['hashKey'];
				$userId =$_GET['userId'];
			}else if(isset($_POST['hashKey']) && isset($_POST['userId']) ){
				$token = $_POST['hashKey'];
				$userId = $_POST['userId'];
			}else{
				exit('{"code":900,"error_msg":"hashKey或userId没有传入"}');
			}
			//拦截器  校验 md5(融云的token + imoment的token )
			//第一步取出 注册表中的token ,imoment的token在db.config.php设置
			$idArray =Tool::slice_userId_ReturnArray($userId);
			$datas =$pdo->select(UserInformation.$idArray[0],[
			'uuid'
			],[
			'User_id'=>$idArray[1]
			]);
			if(empty($datas)){
				exit('{"code":900,"error_msg":"userId错误"}');
			}
			$tokenFromServer;
			foreach($datas as $data){
				$tokenFromServer = $data['uuid']; //这里uuid就是token  不要修改字段名防止其他程序出现错误 
			}
			//第二步  md5()
			$hashKey =md5($tokenFromServer.TOKENFROMSERVER);
			//校验算出来的hashKey 和传入的hashKey($token) 
			if($hashKey!=$token){
				exit('{"code":900,"error_msg":"签名错误"}');
			}
		}
	
		
		return $pdo;

	}
	//根据邮箱更新用户 ,首先要计算出用户在那个表格  ,然后在那个表格找到用户所在的行数 ,组合成表名后缀, 根据后缀 更新表格  
	//*******因为这个方法是类通用的  所以在基类 类中实现 **************
	
	//***第一个是表名前缀常量  第二个是用户邮箱  
	public function returnTableName($table_prefix_const,$UEmail,$pdo){
		$tableID='';
		$uid=sha1($UEmail);
		//这是注册表的表名
		$table_name = Tool::get_table_byTableCount('t_user_info_',$uid,Slice_Table);
		//就是会分10张表 Slice_Table 常量
		$datas = $pdo->select($table_name,[
		'User_id'
		],[
		'UEmail'=>$UEmail
		]
		);
		if(!$datas){
			return ''; //找不到 返回空值    
		}
		foreach($datas as $data){
			$tableID=$data;   //unique约束只有一行 
		}
		return $table_name.$tableID;
	}
	
}

?>