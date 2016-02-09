<?php

//引入数据库中间件 ...controller\DataBaseAccessMiddleLayer
//这个中间件是为了创建类别数据表使用的,好像学校类,话题类,用户注册表(特征是 一个表会对应很多用户,表的索引只能创建一次)
require_once Tool::returnAbsoluteRouteString(['controller','DataBaseAccessMiddleLayer','CreateTableAndIndexForClass.php']);

class UalaisIndex {
	
	static  function getHashTableIdByUalais($Ualais){
		$uid=md5($Ualais);
		return $uid >= 0 ? ($uid%UalaisIndexTableCount)+1:-1;  //常量看db.config.php
	}
	
	static function createTableAndIndex(&$pdo,$Ualais){
		//	static function createTableOnCreateNewClassType($pdo,$message_static_file,$createTableString,$createIndexString,$createTableError='创建数据表失败',$createIndexError='创建消息话题联系表 索引失败');
		$id = UalaisIndex::getHashTableIdByUalais($Ualais);
		//注意临时文件 保存在controller里面
		$message_static_file = 'UalaisIndexTmp'.DIRECTORY_SEPARATOR.$id.'.json';//返回静态文件的路径
		//这里冗余了 个人头像地址和个人介绍 ,因为考虑到用户是在不同t_user_info_表,如果只保留userId,那要搜索的表格会有很多,好像 1_1, 2_2 要搜寻很多张表才能将头像 ,个人简介凑齐 
		// id字段 是因为如果没有自增列 insert返回的序号(结果)一直是0 
		$createTableString='CREATE TABLE IF NOT EXISTS  '.$id.UalaisIndexTableName.'(
				id int not null auto_increment primary key,
				Ualais varchar(20) not null,
				userId varchar(40) not null,
				UimageSrc varchar(150) null,
				Uinfo varchar(240) null
				);';
		$createIndexString='create index '.$id.'_Index on '.$id.UalaisIndexTableName.'(Ualais(20));';
		
		$result = CreateTableAndIndexForClass::createTableOnCreateNewClassType($pdo,$message_static_file,$createTableString,$createIndexString);
		if($result===true){
			return true;
		}else{
			return false;
		}
	}
	
	static function insertToUalaisIndex(&$pdo,$insertTableName,&$insertArray){
		//static function insertToNewClassTypeTable(&$pdo,$insertTableName,$insertArray);
		$result = CreateTableAndIndexForClass::insertToNewClassTypeTable($pdo,$insertTableName,$insertArray);
		if($result===true){
			return true;
		}else{
			return false;
		}
	}
	

	
}

?>