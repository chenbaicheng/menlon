<?php
//
//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'ViewModel'.DIRECTORY_SEPARATOR.'viewModelBaseClass.php';


/* require_once Tool::returnAbsoluteRouteString(['view','Json_encode_decode.php']); */

class getMessageViewModel extends  viewModelBaseClass{

	public function __construct($dbName){    
	   // $parent='medoo_returnPDO';
		return parent::__construct($dbName);
	}

	
/* getSomeMessageByUserId 是获取 发布的消息  而不是获取评论, 
eg: 在以下情况调用这个函数  
	当用户在查看 某个用户的主页(或者      ) 要根据用户id抽取这个用户的发布帖子    
 */

//***********成员变量	 下面是拉取全部评论 根据userId , 9个一页  
	
	static private function getHomeMessageByUserId_Static($message_static_file,&$pdo,$userId,$page){ //pdo要传入 这个类生成的pdo
	//数组转换为对象,因为action函数需要使用这个,他只有一个实参,所以只能使用变量
		
			
					
					//检查表是否已经创建了
					try{ //注意没有$号的 都在db.config.php中定义了的常量
						$datas=$pdo->select(Message_const.$userId,[
						'Msg_id',
						'User_id',
						'ForUalais',
						'Tid',
						'Mcontent',
						'Mhttp',
						'ImgeWidthAndHeight_JSON',
						'location_x',
						'location_y',
						'locationName',
						'extra',
						'Type',
						'Mfav',
						'Commented_count',
						'Transferred_count',
						'Time_t'
						],[
						'ORDER'=>[
						'Time_t DESC'
						],
						'LIMIT'=>[MessagePageSize*$page-MessagePageSize,MessagePageSize*$page]
						]);
						//下面处理从表中取出的数据
						if(!$datas){
							throw new PDOException('userId:'.$userId.'  '.$pdo->last_query());

						}
						foreach($datas as &$data){
							
							//必须要将json转换为数组  否则输出 json会无法解析      
							$data['ImgeWidthAndHeight_JSON'] = JSON_Array_Control::JsonString_to_array($data['ImgeWidthAndHeight_JSON']);
							//var_dump( $data['ImgeWidthAndHeight_JSON']);
							$data['extra'] = JSON_Array_Control::JsonString_to_array($data['extra']); //因为extra是json 必须要将json转换为数组  否则输出 json会无法解析   
						}
						//结果写入静态文本 
						file_put_contents($message_static_file,JSON_Array_Control::array_to_json($datas) );
						return $datas;//直接返回
						
					}catch(PDOException $exception){
						
						Tool::debug_content( $exception->getMessage() );
						return '';
					}
		

    }//end class static function
	
	//上面用来查找,这里
	static function getHomeMessageByUserId(&$pdo,$userId,$page,$refreshBool=''){//默认关闭手动刷新静态文件  $refreshBool=false;
		
		$message_static_file = 'getHomeMessageByUserIdTmp'.DIRECTORY_SEPARATOR.'userMessage_static_file'.$userId.$page.'.json'; //将数组保存成json  静态化 
		$expr = 600; //静态文件有效期,10分钟  如果是10天 请使用 3600*24*10 
		
		
		if(file_exists($message_static_file)){
			
			$file_ctime = filemtime($message_static_file);
			if( ($file_ctime+$expr-->time())&& ($refreshBool=='') ){ //这里使用$refreshBool=='' 判断手动刷新
				return JSON_Array_Control::JsonString_to_array(file_get_contents($message_static_file)); //因为文件保存的是json 字符串 
			}else{ //如果已经过期  
				//从数据库生成文件 
				//unlink($message_static_file);
			//getHomeMessageByUserId_Static(&$pdo,$userId,$page);
				return getMessageViewModel::getHomeMessageByUserId_Static($message_static_file,$pdo,$userId,$page);
			}
		}else{
			//上面会将结果写入文件,这里返回 数组
			return getMessageViewModel::getHomeMessageByUserId_Static($message_static_file,$pdo,$userId,$page);
		}

		return '';
		
		
	}//end class static function
	
	//主页提取结束   
	
	//评论不适宜做静态化 ,但是还是可以使用静态化, 因为消息动态性只对于评价后查看回复有用,对于其他情况,某个人看到一个帖子,及时的消息对于这种角色来说并不需要
	//负责静态化的私有函数 
	static private function getReplyMessageByUserIdByDataTable($message_static_file,&$pdo,$userId,$messageId,$page){
	//首先取出 评论的第一级目录,再取出二级目录, 为什么要先取出1级目录 而不是直接结果集limit 10, 因为评论子节点可能会被删掉(过滤), 所以先取出评论父节点5个,然后根据这5个  
		try{
			$datas = $pdo->select(ReplyMsg_Msg.$userId,'*',[
			'AND'=>[
				'messageId'=>$messageId,     /*这里要创建索引 */
				'dependReplyId'=>0 /* null 为 父节点  ,子节点 会在这个字段记录父节点的位置 */
				],//这里最好使用 0 为默认值,如果使用null 性能会不好   
			'ORDER'=>[
			'replyDateTime ASC'
			],
			'LIMIT'=>[ReplyMessagePageSize*$page-ReplyMessagePageSize,ReplyMessagePageSize*$page]
			]);
			
			if(empty($datas)){
				throw new PDOException('empty');

			}
			foreach ($datas as &$data){ //因为需要修改$datas  所以使用 引用  

					$datas2 = $pdo->select(ReplyMsg_Msg.$userId,
					'*'
					,[
					'AND'=>[
						'messageId'=>$messageId,     /*这里要创建索引 */
						'dependReplyId'=>$data['replyId'] /* 根据父评论id  找出子评论   */
						],
					'ORDER'=>[
					'replyDateTime ASC'  /*评论早的应该放前面 */
					],/*----这里不应该限制回复个数,应该全部取出来,然后按页码生成静态文件 2015/12/31/ 百度贴吧*/
					'limit'=>[ReplyChildrenMessagePageSize*$page-ReplyChildrenMessagePageSize,ReplyChildrenMessagePageSize*$page]
					]
					);  //这个select 语句会抽出 data 对应的子节点  
					//注意这里不需要判断错误,因为有可能读到 没有结果集返回  
/* 					if(!$datas2){
						throw new PDOException('userId:'.$userId.'  '.$pdo->last_query().'  错误信息--json:'.JSON_Array_Control::array_to_json($pdo->error() ) );
					} */
					//这个是评论 插入父节点
					$data['childrenReply']=$datas2;//因为这里要修改$data  所以需要使用引用  

			}
			//结果写入静态文本 
			file_put_contents($message_static_file,JSON_Array_Control::array_to_json($datas) );
			return $datas;
		}catch(PDOException $exception){
			if($exception->getMessage()=='empty'){
				return ['code'=>802,'error_msg'=>'结果为空'];
			}
			Tool::debug_content( $exception->getMessage() );
			return '';  // 返回 '' 说明发送错误 
		}
	}
	static function getReplyMessageByUserId(&$pdo,$userId,$messageId,$page,$refreshBool=''){//默认关闭手动刷新静态文件  $refreshBool=false;
		
		$message_static_file = 'getReplyMessageByUserIdTmp'.DIRECTORY_SEPARATOR.'replyMessage_static_file'.$userId.$messageId.'.json'; //将数组保存成json  静态化 
		$expr = 600; //静态文件有效期,10分钟  如果是10天 请使用 3600*24*10 
		
		
		if(file_exists($message_static_file)){
			
			$file_ctime = filemtime($message_static_file);
			if( ($file_ctime+$expr-->time())&& ($refreshBool=='') ){ //这里使用$refreshBool=='' 判断手动刷新
				return JSON_Array_Control::JsonString_to_array(file_get_contents($message_static_file)); //因为文件保存的是json 字符串 
			}else{ //如果已经过期  
				//从数据库生成文件 
				//unlink($message_static_file);
			
				return getMessageViewModel::getReplyMessageByUserIdByDataTable($message_static_file,$pdo,$userId,$messageId,$page);
			}
		}else{
		
			//上面会将结果写入文件,这里返回 数组
			return getMessageViewModel::getReplyMessageByUserIdByDataTable($message_static_file,$pdo,$userId,$messageId,$page);
		}

		return '';
		
		
	}//end class static function
    
	//**************************获取评论 一级评论  这里不采用二级评论,全部变成一级评论,所有评论在同一个版面上******************************************************
	
	static private function getReplyAndReplyByUserIdMessageId_Static($message_static_file,&$pdo,$userId,$messageId,$page){
		try{
			$datas = $pdo->select(ReplyMsg_Msg.$userId,'*',[
			'AND'=>[
				'messageId'=>$messageId     /*这里要创建索引 */
				],//这里最好使用 0 为默认值,如果使用null 性能会不好   
			'ORDER'=>[
			'replyDateTime ASC'
			],
			'LIMIT'=>[ReplyMessagePageSize*$page-ReplyMessagePageSize,ReplyMessagePageSize*$page]
			]);
			
			if(empty($datas)){
				throw new PDOException('empty');

			}
		
			//结果写入静态文本 
			file_put_contents($message_static_file,JSON_Array_Control::array_to_json($datas) );
			return $datas;
		}catch(PDOException $exception){
			if($exception->getMessage()=='empty'){
				return ['code'=>802,'error_msg'=>'结果为空'];
			}
			Tool::debug_content( $exception->getMessage() );
			return '';  // 返回 '' 说明发送错误 
		}
	}
	//    只有一个层 的评论
	static function getReplyMessageBy_OneLayer(&$pdo,$userId,$messageId,$page,$refreshBool=''){
			$message_static_file = 'getReplyMessageBy_OneLayerTmp'.DIRECTORY_SEPARATOR.'replyMessage_static_file'.$userId.$messageId.'.json'; //将数组保存成json  静态化 
		$expr = 600; //静态文件有效期,10分钟  如果是10天 请使用 3600*24*10 
		
		
		if(file_exists($message_static_file)){
			
			$file_ctime = filemtime($message_static_file);
			//var_dump(($refreshBool) );
			if( ($file_ctime+$expr-->time())&& ($refreshBool=='') ){ //这里使用$refreshBool=='' 判断手动刷新
				return JSON_Array_Control::JsonString_to_array(file_get_contents($message_static_file)); //因为文件保存的是json 字符串 
			}else{ //如果已经过期  
				//从数据库生成文件 
				//unlink($message_static_file);

				return getMessageViewModel::getReplyAndReplyByUserIdMessageId_Static($message_static_file,$pdo,$userId,$messageId,$page);
			}
		}else{
		
			//上面会将结果写入文件,这里返回 数组
			return getMessageViewModel::getReplyAndReplyByUserIdMessageId_Static($message_static_file,$pdo,$userId,$messageId,$page);
		}

		return '';
	}
	
	/****************end   一级评论  *************/
	
	//禁止使用下面的getMessageToPhotoList()函数  
	//**** 这是获取图片列表的信息  ,注意评论只取第一个 (不要使用帖子和评论放在同一个框那种方法,运算成本高 要搜两个表) ,这个是基础模板  
	static function getMessageToPhotoList(&$pdo,$userId,$messageId){
		try{
			$datas = $pdo->select(Message_const.$userId,'*',[
			'Msg_id'=>$messageId,
			'ORDER'=>['Time_t DESC'],
			'LIMIT'=>1
			]);
			//下面, 这是抽取消息的评论  
			if(!$datas){ //这里不抛出错误 ,只是记录下来,因为 $messageId 是由服务器提供,如果出错,一是攻击,而是服务器错误  
				Tool::debug_content( 'userId:'.$userId.'  '.$pdo->last_query().'  错误信息--json:'.JSON_Array_Control::array_to_json($pdo->error()) );
				return '';  //返回错误  
			}
			$datas2 = $pdo->select(ReplyMsg_Msg.$userId,'*',[
			'messageId'=>$messageId,
			'LIMIT'=>1
			]);
			//因为存在评论为空的情况, 所以不用判断结果集
			$datas['replyMessage']=$datas2;
			return $datas;
		}catch(PDOException $exception){
			Tool::debug_content( $exception->getMessage() );
			return '';  // 返回 '' 说明发送错误 
		}
		return ''; //错误返回 
	}//end class static function   
}

?>