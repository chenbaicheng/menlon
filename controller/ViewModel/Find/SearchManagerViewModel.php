<?php

//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'ViewModel'.DIRECTORY_SEPARATOR.'viewModelBaseClass.php'; //class Tool


class SearchManagerViewModel extends  viewModelBaseClass{

	public function __construct($dbName){    
	    $parent='viewModelBaseClass';
		
		return $parent::__construct($dbName);
	}
	
	//这里的page 控制着页数  
	static private function searchByHotValueByTable(&$message_static_file,&$pdo,&$page){
			try{
				$messageArray=[];//对用户userId集 再次排序  
				$resultUserIdArray=[];
				//$resultUserIdArray=[];
				for($i=1;$i<=Slice_Table;++$i){
					//var_dump('ssssssssss'.$resultUserIdArray[1]);
						try{
							$resultUserIdArray[$i]=$pdo->select(UserInformation.$i,'User_id',[
							'ORDER'=>[
									'Fans_count DESC',
									'Msg_count DESC'
							],
							'LIMIT'=>[$page*SEARCH_GET_USERID_COUNT_FOR_SEARCH-SEARCH_GET_USERID_COUNT_FOR_SEARCH,$page*SEARCH_GET_USERID_COUNT_FOR_SEARCH]
							]); //插入数组
						//var_dump($resultUserIdArray[$i]); 							
						}catch(PDOException $exception){
							//不处理错误 
						}
				}
				
				//var_dump($resultUserIdArray);
				//对用户userId集 再次排序,根据userId 分别在这些用户的帖子表 找出时间最新的帖子 
				//$messageArray=[];  //if里面作用域只有if里面
				foreach($resultUserIdArray as $key=>$datas){ //键名是前缀,值是后缀
					foreach($datas as $data){ //$datas 是每个用户表的结果集 
						//在这里根据 每个用户的userId 抽取消息表的消息
						try{
							$tableName = $key.'_'.$data;//这里的$key<看上面$i>是用户注册表所在序号 $data是后缀 所在行数,
							
							$result = $pdo->select(Message_const.$tableName,[
							'Msg_id','User_id','ForUalais','Tid','Cid','Mcontent','Mhttp','extra','Mfav','Commented_count','Transferred_count','Time_t'
							],[
							'ORDER'=>[
									'Time_t DESC',
									'Commented_count DESC',
									'Transferred_count DESC',
									'Mfav DESC'
							],
							'LIMIT'=>[0,1]
							]);
							//echo $pdo->last_query();
					
							if(!empty($result)){
								$messageArray[$key]=$result[0];//注意这里使用了 下标0 是因为上面只取出一条记录,如果取出多条,需要
							}else{
								throw new PDOException('查询失败');
							}
						}catch(PDOException $exception){
							//不处理错误 
						}
						
					}
				}
				//生成数据完毕 
				//写入文件 生成静态文本 
			
				file_put_contents($message_static_file,JSON_Array_Control::array_to_json($messageArray) );

			}catch(PDOException $exception){
				/* Tool::debug_content( $exception->getMessage() );
				
				return ''; */
				//不处理 找不到表的异常抛出,因为刚开始不可能会有全部表都建立完成 
			}
			return $messageArray;
	}
	
	//注意这里的数据库 都是select 所以不用事务处理 
	static public function searchByHotValue(&$pdo,$page){
		//这里是对 用户注册表 所在数据库进行操作  ,注意 注册表是分表,所以要遍历提取 
		// 提取 评论次数和转发次数 ,前十的用户  
		
		$message_static_file = 'searchByHotValuetmp'.DIRECTORY_SEPARATOR.'message_static_file_'.$page.'.json'; //将数组保存成json  静态化 
		$expr = 900; //静态文件有效期,半个小时  如果是10天 请使用 3600*24*10 
		
		
		if(file_exists($message_static_file)){
			//clearstatcache();
			$file_ctime = filemtime($message_static_file);
			//var_dump( $file_ctime+$expr .'&'.time() );
			if($file_ctime+$expr -->time()){
				
				return JSON_Array_Control::JsonString_to_array(file_get_contents($message_static_file)); //因为文件保存的是json 字符串 
			}else{ //如果已经过期  
				//从数据库生成文件 
			//不保存 userId的json 只保留message的json
				return SearchManagerViewModel::searchByHotValueByTable($message_static_file,$pdo,$page);
			}
		}else{
		
			//上面会将结果写入文件,这里返回 数组
			return SearchManagerViewModel::searchByHotValueByTable($message_static_file,$pdo,$page);
		}

		return '';
	} //end function 
	
	
	
	//根据话题总表,按照转发,时间 评论进行排序 选出10个结果    
	static private function searchByMessageTitleByTable($message_static_file,&$pdo,$page,$getNumber){
		try{
			//首先根据 messagetitle表 取出前10个话题 
			$datas= $pdo->select(MessageTitle,[
			'Tid','Tname'/*这里取出Tid和Tname 是为了由话题编号找出这个话题的所有帖子时候,界面需要一个话题名称Tname*/
			],[
			'ORDER'=>['TmessageCount DESC','TsubmitTime DESC'],
			'LIMIT'=>[HOTTITLECOUNT*$page-HOTTITLECOUNT,HOTTITLECOUNT*$page]
			]);
			if(empty($datas)){
				throw new PDOException('结果为空 来自搜索前10个话题');
			}
			//结果不为空 ,根据前面的到的 话题编号Tid,在那个话题中提取
			//在10个话题中提取出第一个帖子,生成静态文件,因为不可能频繁的遍历数据表
			//$resultArray=[];
			$datas3=[];
			$i=1;
			foreach($datas as $data){
				/*如果写成 $datas2 在遍历完成后,会被覆盖掉,所以使用其他方法传出去 使用$datas3=[];*/
				$datas2=$pdo->select(MESSAGE_TID_RELATIONSHIP.$data['Tid'],
				'*'
				,[
				'ORDER'=>['dtime DESC','Commented_count DESC','Mfav DESC'],//组合索引
				'LIMIT'=>[0,$getNumber]//只取出一条记录
				]);
				
				/*  
				var_dump($datas2); //下面是$datas2的输出 ,注意数组只有1个 下标为0  ,这里select结果需要使用foreach循环  
				array(1) { [0]=> array(8) { ["id"]=> string(1) "1" ["userId"]=> string(3) "1_1" ["messageId"]=> string(1) "1" ["Mcontent"]=> string(0) "" ["Mhttp"]=> string(0) "" ["dtime"]=> string(19) "2015-12-24 08:18:46" ["Commented_count"]=> string(1) "0" ["Mfav"]=> string(1) "0" } } array(1) { [0]=> array(8) { ["id"]=> string(1) "1" ["userId"]=> string(3) "1_1" ["messageId"]=> string(1) "2" ["Mcontent"]=> string(0) "" ["Mhttp"]=> string(0) "" ["dtime"]=> string(19) "2015-12-24 08:19:34" ["Commented_count"]=> string(1) "0" ["Mfav"]=> string(1) "0" } } 
				*/
				if(empty($datas2)){ 
					//这里的错误必须捕获 ,因为前面的话题的序号是存在的,所以一定会有消息存在
					//不过考虑到 有一个用户新建了话题,然后他把帖子给删除了,这样只剩下话题表 但是已经没有消息了
					//所以这里的错误还是不抛出了 ,因为这里没有事务  所以抛出错误没有关系 
					throw new PDOException('结果为空 来自搜索话题的第一个帖子');
				}else{
				//如果没有出错 继续执行  
				//将Tid 和Tname放入帖子数组
				//注意不为空 才能进行数组操作  
					//如果上面取出了两个及以上 下面会出现问题,因为不会添加到数组,现在添加多一个序号 
					$j=1;
					foreach($datas2 as $data2){ //循环前面select 出来的数组 
						//这里是使用一个二维数组 重新填充格式 
						$datas3[$i][$j]['Tid']=$data['Tid'];
						$datas3[$i][$j]['Tname']=$data['Tname'];
						$datas3[$i][$j]['id']=$data2['id'];//好像id没有什么意义
						$datas3[$i][$j]['userId']=$data2['userId'];
						$datas3[$i][$j]['messageId']=$data2['messageId'];
						$datas3[$i][$j]['Mcontent']=$data2['Mcontent'];
						$datas3[$i][$j]['Mhttp']=$data2['Mhttp'];
						$datas3[$i][$j]['Commented_count']=$data2['Commented_count'];
						$datas3[$i][$j]['Mfav']=$data2['Mfav'];
						$datas3[$i][$j]['extra']=$data2['extra'];
						++$j;
					}
					//每遍历完成后,向$datas3 添加  
					//var_dump($datas2);
					
					++$i;
				}
			
				
			}//$resultArray 就是结果集  
			//结果写入静态文本 
			file_put_contents($message_static_file,JSON_Array_Control::array_to_json($datas3) );
			//返回结果集 
			return $datas3;
		}catch (PDOException $exception){
			Tool::debug_content( $exception->getMessage() );
			return ''; //错误都返回 '' ,controller会处理错误  
		}

	}
	//热门话题拉取....
	static function searchByHotMessageTitle(&$pdo,$page=1,$getNumber=1){
		
		$message_static_file = 'searchByHotTalkContenttmp'.DIRECTORY_SEPARATOR.'message_static_file_'.$page.'.json'; //将数组保存成json  静态化 
		$expr = 900; //静态文件有效期,15分钟  如果是10天 请使用 3600*24*10 
		
		
		if(file_exists($message_static_file)){
			
			$file_ctime = filemtime($message_static_file);
			if($file_ctime+$expr-->time()){
				return JSON_Array_Control::JsonString_to_array(file_get_contents($message_static_file)); //因为文件保存的是json 字符串 
			}else{ //如果已经过期  
				//从数据库生成文件 
				//unlink($message_static_file);
				return SearchManagerViewModel::searchByMessageTitleByTable($message_static_file,$pdo,$page,$getNumber);
			}
		}else{
		
			//上面会将结果写入文件,这里返回 数组
			return SearchManagerViewModel::searchByMessageTitleByTable($message_static_file,$pdo,$page,$getNumber);
		}

		return '';
	}
	
	//这是热门话题的私有方法  ,用来生成静态文件和返回结果 
	static private function getMessageByTidByDataTable($message_static_file,&$pdo,$Tid,$page){
		try{
			
			$datas=$pdo->select(MESSAGE_TID_RELATIONSHIP.$Tid,
			'*'
			,[
			'ORDER'=>['dtime DESC','Commented_count DESC','Mfav DESC'],//组合索引
			'LIMIT'=>[GETMESSAGE_COUNT*$page-GETMESSAGE_COUNT,GETMESSAGE_COUNT*$page]//只取出一条记录
			]);
			if(empty($datas)){ 
				//这里的错误必须捕获 ,因为前面的话题的序号是存在的,所以一定会有消息存在
				//防止错误传人不正确的userId      
				throw new PDOException('结果为空 话题id错误或话题内无帖子');
			}

			file_put_contents($message_static_file,JSON_Array_Control::array_to_json($datas) );
			return $datas; //直接返回结果    
		}catch (PDOException $exception){
			Tool::debug_content( $exception->getMessage() );
			return ''; //错误都返回 '' ,controller会处理错误  
		}
	}
	
	//这是热门话题以后的处理,或者通用方法  
	static function getMessageByTid(&$pdo,$Tid,$page){
		$message_static_file = 'getMessageByTidtmp'.DIRECTORY_SEPARATOR.'tidMessage_static_file'.$Tid.'_'.$page.'.json'; //将数组保存成json  静态化 
		$expr = 900; //静态文件有效期,这里应该和前面的 searchByHotMessageTitle()相同的过期时间 如果是10天 请使用 3600*24*10 
		
		
		if(file_exists($message_static_file)){
			
			$file_ctime = filemtime($message_static_file);
			if($file_ctime+$expr-->time()){
				return JSON_Array_Control::JsonString_to_array(file_get_contents($message_static_file)); //因为文件保存的是json 字符串 
			}else{ //如果已经过期  
				//从数据库生成文件 
				//unlink($message_static_file);
				return SearchManagerViewModel::getMessageByTidByDataTable($message_static_file,$pdo,$Tid,$page);
			}
		}else{
			//上面会将结果写入文件,这里返回 数组
			return SearchManagerViewModel::getMessageByTidByDataTable($message_static_file,$pdo,$Tid,$page);
		}

		return '';
	}
	
	static private function searchByHotSchoolTitleByTable($message_static_file,&$pdo,$page,$getNumber){
		try{
			//首先根据 messagetitle表 取出前10个话题 
			$datas= $pdo->select(SCHOOLTITLE,[
			'schoolId','schoolName'/*这里取出Tid和Tname 是为了由话题编号找出这个话题的所有帖子时候,界面需要一个话题名称Tname*/
			],[
			'ORDER'=>['schoolMessageCount DESC'],
			'LIMIT'=>[HOTSCHOOLCOUNT*$page-HOTSCHOOLCOUNT,HOTSCHOOLCOUNT*$page]
			]);
			if(empty($datas)){
				throw new PDOException('结果为空 来自搜索前'.HOTSCHOOLCOUNT.'个 学校');
			}
			//结果不为空 ,根据前面的到的 话题编号Tid,在那个话题中提取
			//在10个话题中提取出第一个帖子,生成静态文件,因为不可能频繁的遍历数据表
			//$resultArray=[];
			$datas3=[];
			$i=1;
			foreach($datas as $data){
				/*如果写成 $datas2 在遍历完成后,会被覆盖掉,所以使用其他方法传出去 使用$datas3=[];*/
				$datas2=$pdo->select(MESSAGE_SCHOOL_RELATIONSHIP.$data['schoolId'],
				'*'
				,[
				'ORDER'=>['dtime DESC','Commented_count DESC','Mfav DESC'],//组合索引
				'LIMIT'=>[0,$getNumber]//只取出一条记录
				]);
				
				if(empty($datas2)){ 
					//这里的错误必须捕获 ,因为前面的话题的序号是存在的,所以一定会有消息存在
					//不过考虑到 有一个用户新建了话题,然后他把帖子给删除了,这样只剩下话题表 但是已经没有消息了
					//所以这里的错误还是不抛出了 
					throw new PDOException('结果为空 来自搜索学校的第一个帖子');
				}else{
				//如果没有出错 继续执行  
				//将Tid 和Tname放入帖子数组
				//注意不为空 才能进行数组操作  
					$j=1;
					foreach($datas2 as $data2){ //循环前面select 出来的数组 
						//这里是使用一个二维数组 重新填充格式 
						$datas3[$i][$j]['schoolId']=$data['schoolId'];
						$datas3[$i][$j]['schoolName']=$data['schoolName'];
						$datas3[$i][$j]['id']=$data2['id'];//好像id没有什么意义
						$datas3[$i][$j]['userId']=$data2['userId'];
						$datas3[$i][$j]['messageId']=$data2['messageId'];
						$datas3[$i][$j]['Mcontent']=$data2['Mcontent'];
						$datas3[$i][$j]['Mhttp']=$data2['Mhttp'];
						$datas3[$i][$j]['Commented_count']=$data2['Commented_count'];
						$datas3[$i][$j]['Mfav']=$data2['Mfav'];
						$datas3[$i][$j]['extra']=$data2['extra'];
						++$j;
					}
					//每遍历完成后,向$datas3 添加  
					//var_dump($datas2);
					
					++$i;
				}
			
				
			}//$resultArray 就是结果集  
			//结果写入静态文本 
			file_put_contents($message_static_file,JSON_Array_Control::array_to_json($datas3) );
			//返回结果集 
			return $datas3;
		}catch (PDOException $exception){
			Tool::debug_content( $exception->getMessage() );
			return ''; //错误都返回 '' ,controller会处理错误  
		}
	}
	//按学校热度 搜索  返回前10名学校  ,也可以分页返回 
	static function searchByHotSchoolTitle(&$pdo,$page=1,$getNumber=1){
		$message_static_file = 'searchByHotSchoolTitletmp'.DIRECTORY_SEPARATOR.'message_static_file_'.$page.'.json'; //将数组保存成json  静态化 
		$expr = 900; //静态文件有效期,15分钟  如果是10天 请使用 3600*24*10 
		
		
		if(file_exists($message_static_file)){
			
			$file_ctime = filemtime($message_static_file);
			if($file_ctime+$expr-->time()){
				return JSON_Array_Control::JsonString_to_array(file_get_contents($message_static_file)); //因为文件保存的是json 字符串 
			}else{ //如果已经过期  
				//从数据库生成文件 
				//unlink($message_static_file);
				return SearchManagerViewModel::searchByHotSchoolTitleByTable($message_static_file,$pdo,$page,$getNumber);
			}
		}else{
		
			//上面会将结果写入文件,这里返回 数组
			return SearchManagerViewModel::searchByHotSchoolTitleByTable($message_static_file,$pdo,$page,$getNumber);
		}

		return '';
	}
	
	
		//这是热门学校的私有方法  ,用来生成静态文件和返回结果 
	static private function getMessageBySchoolIdByDataTable($message_static_file,&$pdo,$Tid,$page){
		try{
			
			$datas=$pdo->select(MESSAGE_SCHOOL_RELATIONSHIP.$Tid,
			'*'
			,[
			'ORDER'=>['dtime DESC','Commented_count DESC','Mfav DESC'],//组合索引
			'LIMIT'=>[GETMESSAGE_COUNT*$page-GETMESSAGE_COUNT,GETMESSAGE_COUNT*$page]//只取出一条记录
			]);
			
			if(empty($datas)){ 
				//这里的错误必须捕获 ,因为前面的话题的序号是存在的,所以一定会有消息存在
				//防止错误传人不正确的userId      
				throw new PDOException('emptydata');
			}

			file_put_contents($message_static_file,JSON_Array_Control::array_to_json($datas) );
			return $datas; //直接返回结果    
		}catch (PDOException $exception){
			
			if($exception->getMessage()=='emptydata'){
				return ['code'=>801,'error_msg'=>'结果为空 话题id错误/页码错误/话题内无帖子'];
			}
			Tool::debug_content( $exception->getMessage() );
			return ''; //错误都返回 '' ,controller会处理错误  
			
			
		}
		
	}
	
	//根据schoolId获取 这个学校的所有帖子,按时间 评论次数  点赞 来降序 ,分页 
	static function getMessageBySchoolId(&$pdo,$schoolId,$page){
		$message_static_file = 'getMessageByschoolIdTmp'.DIRECTORY_SEPARATOR.'schoolMessage_static_file'.$schoolId.'_'.$page.'.json'; //将数组保存成json  静态化 
		$expr = 900; //静态文件有效期,这里应该和前面的 searchByHotMessageTitle()相同的过期时间 如果是10天 请使用 3600*24*10 
		
		
		if(file_exists($message_static_file)){
			
			$file_ctime = filemtime($message_static_file);
			if($file_ctime+$expr-->time()){
				return JSON_Array_Control::JsonString_to_array(file_get_contents($message_static_file)); //因为文件保存的是json 字符串 
			}else{ //如果已经过期  
				//从数据库生成文件 
				//unlink($message_static_file);
				return SearchManagerViewModel::getMessageBySchoolIdByDataTable($message_static_file,$pdo,$schoolId,$page);
			}
		}else{
			//上面会将结果写入文件,这里返回 数组
			return SearchManagerViewModel::getMessageBySchoolIdByDataTable($message_static_file,$pdo,$schoolId,$page);
		}

		return '';
	}
	
}

?>