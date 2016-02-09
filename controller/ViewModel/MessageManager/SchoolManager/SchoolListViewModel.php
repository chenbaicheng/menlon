<?php

//引入数据库访问层 (中间件)
require_once Tool::returnAbsoluteRouteString(['controller','DataBaseAccessMiddleLayer','CreateTableAndIndexForClass.php']);

class SchoolListViewModel extends  viewModelBaseClass{ //他是在ViewModel内嵌使用,所以不用再次引入基类
	//将新学校插入学校列表,并且如果是新学校 就插入总表,如果不是就不插入 ,因为考虑到学校只有300多个  所以就不使用静态文件了<防止高I/O >,直接使用数据库 
	function insertToSchoolList(&$pdo,$schoolId,$schoolName){
		//这里使用事务处理那个pdo链接  因为现在就是在插入数据库,插入总表中 
		try{
			$datas = $pdo->select(SCHOOLTITLE,[
			'schoolId'
			],[
			'schoolId'=>$schoolId
			]);
			if(empty($datas)){
				//throw new PDOException('notFind');
				//如果为空 就插入学校简介
				$conditionCheck = $pdo->insert(SCHOOLTITLE,[
				'schoolId'=>$schoolId,
				'schoolName'=>$schoolName,
				'submitTime'=>TimeForChina
				]);
				if(!$conditionCheck){
					throw new PDOException($pdo->last_query().'--来自SchoolListViewModel');
				}
				//返回 true 表示已经插入 或者原来就存在  
				return true;
			}else{
				//不为空  返回 true 表示已经插入 或者原来就存在  
				return true;
			}
			
		}catch(PDOException $exception){
			Tool::debug_content( $exception->getMessage() );
			return false;
		}
	}
	
	function createTableOnCreateNewSchoolIndex(&$newpdo,$schoolId){
		 //配置文件 
//索引记录文件名
		$staticfileName= 'schoolIndexTmp'.DIRECTORY_SEPARATOR.MESSAGE_SCHOOL_RELATIONSHIP.$schoolId.'.json';
		//表格创建字符串
		$createTableString= 'CREATE TABLE IF NOT EXISTS  '.MESSAGE_SCHOOL_RELATIONSHIP.$schoolId.'(
						id int not null primary key  auto_increment,
						userId varchar(40) not null,
						ForUalais varchar(20) not null,
						messageId int not null,
						Mcontent varchar(150) not null,
						Mhttp varchar(150) not null,
						dtime datetime not null,
						Commented_count int default 0,
						Mfav int default 0,
						extra varchar(512) null,
						CONSTRAINT '.MESSAGE_SCHOOL_RELATIONSHIP.$schoolId.'_unique'.' UNIQUE (userId,messageId)
						 );';
		//索引字符串
		$createIndexString = 'create index indexFor'.MESSAGE_SCHOOL_RELATIONSHIP.$schoolId.' 
						on '.MESSAGE_SCHOOL_RELATIONSHIP.$schoolId.' (
						dtime DESC,Commented_count DESC,Mfav DESC
						);';
		//利用中间层 创建表和索引 ,这里使用这个类自身对象 ,因为防止影响到事务回滚 利用新的pdo链接创建表 
		
		$conditionCheck= CreateTableAndIndexForClass::createTableOnCreateNewClassType(
		$newpdo,$staticfileName,$createTableString,$createIndexString
		);
		if(!$conditionCheck){ //发生错误 
			return false;
		}
		return true;
	}
	
	//这里使用 sentMessageViewModel的pdo链接 
	function insertMessageToSchoolTable(&$pdo,$insertTableName,&$insertArray,$insertErrorContent){
		$conditionCheck= CreateTableAndIndexForClass::insertToNewClassTypeTable(
			$pdo,$insertTableName,$insertArray,$insertErrorContent
			);
		if(!$conditionCheck){ //发生错误 
			return false;
		}
		return true;
	}
	
	//更新此用户所在学校表的发帖数量 
	function updateSchoolTitleMessageCount(&$pdo,$schoolId,$userId){
		try{
			$conditionCheck2=$pdo->update(SCHOOLTITLE,[/*和上面的区别是 事务只能用同一个$pdo */
			'schoolMessageCount[+]'=>1
			],[
			'schoolId'=>$schoolId
			]);
			if($conditionCheck2==0){//等于0相当于没有更新 
				throw new PDOException('userId:'.$userId.'  '.$pdo->last_query());
			}
			return true;
		}catch(PDOException $exception){
			Tool::debug_content( $exception->getMessage() );
			return false;
		}
	}

}

?>