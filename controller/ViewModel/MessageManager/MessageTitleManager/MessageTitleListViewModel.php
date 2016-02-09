<?php
//
//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'ViewModel'.DIRECTORY_SEPARATOR.'viewModelBaseClass.php';

//引入数据库访问层 (中间件)
require_once Tool::returnAbsoluteRouteString(['controller','DataBaseAccessMiddleLayer','CreateTableAndIndexForClass.php']);

/* require_once Tool::returnAbsoluteRouteString(['view','Json_encode_decode.php']); */

class MessageTitleListViewModel extends  viewModelBaseClass{

	public function __construct($dbName){    
	   // $parent='medoo_returnPDO';
		return parent::__construct($dbName);
	}
	//将新话题插入话题列表
	function insertToTitleList(&$pdo,$messageTitleArray,$userId){
		try{
			$result=$pdo->insert(MessageTitle,$messageTitleArray);
			if(!$result){
				throw new PDOException('userId:'.$userId.'  '.$pdo->last_query());
			}
			return $result;//这是新话题序号,用来定位帖子 插入那个数据表 
		}catch(PDOException $exception){
			Tool::debug_content( $exception->getMessage() );
			return false;
		}
		
		
	}
	private $tid;
	// 创建数据表 MESSAGE_TID_RELATIONSHIP ,这个是在用户新建一个话题时候调用的 ...
	function createTableOnCreateNewTid(&$pdo,$tid){
		$this->tid = $tid;
		$staticfileName = 'createMessageTitleIndexTmp'.DIRECTORY_SEPARATOR.MESSAGE_TID_RELATIONSHIP.$this->tid.'.json';
		$createTableString = 'CREATE TABLE IF NOT EXISTS  '.MESSAGE_TID_RELATIONSHIP.$this->tid.'(
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
							CONSTRAINT '.MESSAGE_TID_RELATIONSHIP.$this->tid.'_unique'.' UNIQUE (userId,messageId)
							 );';
		$createIndexString = 'create index indexFor'.MESSAGE_TID_RELATIONSHIP.$this->tid.' 
							on '.MESSAGE_TID_RELATIONSHIP.$this->tid.' (
							dtime DESC,Commented_count DESC,Mfav DESC
							);';
		$createTableError = '创建消息 话题联系表 失败';
		$createIndexError = '创建消息话题联系表 索引失败';
		$conditionCheck1 = CreateTableAndIndexForClass::createTableOnCreateNewClassType($pdo,$staticfileName,$createTableString,$createIndexString,$createTableError,$createIndexError);
		if($conditionCheck1===true){
			return ['code'=>200,'Tid'=>$this->tid];
		}else{
			return ''; //错误返回 
		}
	}
	
	//每发送一个消息 都插入相应的话题表中 , 注意这里是另外一个数据库  ,但是现在使用同一个数据库,最好也使用同一个,因为action不能同时处理多个事务  
	function addMessageTitleListToDataTable(&$pdo,$tid,&$insertArray){ //pdo要传入 这个类生成的pdo
	//数组转换为对象,因为action函数需要使用这个,他只有一个实参,所以只能使用变量
		$insertTableName = MESSAGE_TID_RELATIONSHIP.$tid;
		
		$insertErrorContent = 'userId:'.$insertArray['userId'].'  '.$pdo->last_query() ;//错误返回的样式
		
		$conditionCheck= CreateTableAndIndexForClass::insertToNewClassTypeTable(
			$pdo,$insertTableName,$insertArray,$insertErrorContent
			);
		if(!$conditionCheck){ //发生错误 
			return false;
		}
	
		//处理完成 返回 true			
		return true;
	


    }   //end class function
	
	function updateTitleMessageCount(&$pdo,$tid,$userId){
		//在插入了话题表以后,应该要更新话题总表中 此话题的发帖数量 ,用来做热门话题推送 
		try{
			$conditionCheck2=$pdo->update(MessageTitle,[/*和上面的区别是 事务只能用同一个$pdo */
			'TmessageCount[+]'=>1
			],[
			'Tid'=>$tid
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
	
	//在查找话题时候使用 
	//这里根据话题的名称找出Tid  这是帖子发布的时候使用 ,获取可能得话题标签    
	function getMessageTitleList($pdo,$messageTitleName,$page){ //pdo要传入 这个类生成的pdo
	//数组转换为对象,因为action函数需要使用这个,他只有一个实参,所以只能使用变量
		
	
					//检查表是否已经创建了
					try{ //注意没有$号的 都在db.config.php中定义了的常量
					//话题表没有分表  
						$datas=$pdo->select(MessageTitle,[
						'Tname',
						'Tcontent'
						],[
						'Tname[~]'=>$messageTitleName.'%',
						'LIMIT'=>[10*$page-10,10*$page]
						]);
						//下面处理从表中取出的数据
						if(!$datas){
							throw new PDOException('userId:'.$userId.'  '.$pdo->last_query());
						
						}
						
						return $datas;//直接返回
						
					}catch(PDOException $exception){
						
						Tool::debug_content( $exception->getMessage() );
						return '';
					}
		

    }//end class function
}

?>