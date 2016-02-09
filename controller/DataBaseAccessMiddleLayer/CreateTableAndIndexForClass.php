<?php
//
//***** 
//***** 
//*****
//***************
//不需要引入 medoo_returnPDO.php 因为他是作为插件类拓展功能 只能在viewModel中使用,viewModel已经引入此文件
/* require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'databaseAccessProxy_Model'.DIRECTORY_SEPARATOR.'medoo_returnPDO.php'; //class Tool */

/* require_once Tool::returnAbsoluteRouteString(['view','Json_encode_decode.php']); */

//注意这个只能用在n个用户建立的类别 , 例如话题  学校的分表  ,因为此类表只能创建一个所以索引不能重复创建 
class CreateTableAndIndexForClass{

	static function createTableOnCreateNewClassType(&$pdo,$message_static_file,$createTableString,$createIndexString,$createTableError='创建数据表失败',$createIndexError='创建消息话题联系表 索引失败'){
	
				try{ //注意没有$号的 都在db.config.php中定义了的常量  
					// 拉取话题的时候 应该是时间优先级,而不是根据转发和评论来决定优先级 ,信息的时效性
					
					$conditionCheck1 = $pdo->query($createTableString); //这里 userId和messageId创建unique,同时会创建组合索引 
					if($conditionCheck1===false){//如果重复创建表不会返回false,所以这里错误抛出是其他错误
						throw new PDOException($createTableError);
					}else{
						//$message_static_file = $staticfileName; //将数组保存成json  静态化 
						
						if(!file_exists($message_static_file)){ //如果存在文件就说明索引已经建立					
						//创建索引  , 记得select 语句where条件一定要是索引 否则会出现死锁
				//建立索引     不用担心索引没有创建,如果失败 用户需要再次提交 
							$conditionCheck1 = $pdo->query($createIndexString );//order by 索引  使用触发器更新
							if($conditionCheck1===false){
								throw new PDOException($createIndexError);
							}
							file_put_contents($message_static_file,JSON_Array_Control::array_to_json(['code'=>200,'content'=>'索引创建成功']) );//成功创建索引后,写入内容到对应静态文件中
							
						}
				

					}
					
					return true;
				}catch(PDOException $exception){
				
					//echo $pdo->last_query();
					Tool::debug_content( $exception->getMessage() );
					//return false;
					return false;
				}
				

	}
	
	//每发送一个消息 都插入相应的话题表中 , 注意这里是另外一个数据库  ,但是现在使用同一个数据库,最好也使用同一个,因为action不能同时处理多个事务  
	static function insertToNewClassTypeTable(&$pdo,$insertTableName,&$insertArray,$insertErrorContent='来自插入表信息的错误--CreateTableIndexForClass'){ 
					try{ //注意没有$号的 都在db.config.php中定义了的常量
					//话题与帖子联系表 用帖子id做表后缀     
						$datas=$pdo->insert($insertTableName,$insertArray);
						//下面处理从表中取出的数据
						if(!$datas){
							throw new PDOException($insertErrorContent);
						}
						
						return true;//直接返回    
						
					}catch(PDOException $exception){
						
						Tool::debug_content( $exception->getMessage() );
						return false;
					}
		

    }//end class function
	
	
}

?>