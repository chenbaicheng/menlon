<?php
//
//***** 
//***** 
//*****
//***************

/* require_once Tool::returnAbsoluteRouteString(['view','Json_encode_decode.php']); */
//注意这是插件类  需要在viewModel下运行,不能单独使用 
class updateDynamicCountPlug_in { //这里负责更新计数统计  ,包含点赞更新(收藏更新)  
	//事务处理不是插件类处理  交由调用此插件的ViewModel 负责事务   
	//下面的$id 是消息在所在Tid表中的位置  
	static function updateFavCount(&$pdo,$Tid,$id,$fromUserId,$messageId){
		try{
			//这是更新用户消息总表 
			$conditionCheck1 =$pdo->update(Message_const.$fromUserId,[
							'Mfav[+]'=>1
							],[
							'Msg_id'=>$messageId
							]);
			if(!$conditionCheck1){
				throw new PDOException($pdo->last_query().'   来自updateDynamicCount');
			}
			//这是更新话题表 点赞数  
/* 			$conditionCheck1 =$pdo->update(MESSAGE_TID_RELATIONSHIP.$Tid,[
							'Mfav[+]'=>1
							],[
							'AND'=>[
			//这里的优化建议 使用id  主键列进行搜索  key_len长度比 4(primary key):126(unique索引) 
								'userId'=>$fromUserId,
								'messageId'=>$messageId
								]
							]); */
			$conditionCheck1 =$pdo->update(MESSAGE_TID_RELATIONSHIP.$Tid,[
							'Mfav[+]'=>1
							],[
			//虽然这里的结果 和上面一样 type=range 但是 key_len 明显变少 
							'id'=>$id
							]);
			
			if(!$conditionCheck1){
				throw new PDOException($pdo->last_query().'   来自updateDynamicCount--'.MESSAGE_TID_RELATIONSHIP.$Tid);
			}
			//更新学校表 
			/***首先找出此用户的学校id,如果没有就直接跳过不处理*****/
			$idArray=Tool::slice_userId_ReturnArray($fromUserId);
			$datas = $pdo->select(UserInformation.$idArray[0],[
			'UschoolIndex'
			],[
			'User_id'=>$idArray[1]
			]);
			/****在找到schoolId以后 根据UschoolIndex,messageId 在学校分表找出那条消息,然后计数加一 *****/
			if(!empty($datas)){
				foreach($datas as $data){
					$schoolId=$data['UschoolIndex'];
				}
				$conditionCheck1 = $pdo->update(MESSAGE_SCHOOL_RELATIONSHIP.$schoolId,[
				'Mfav[+]'=>1
				],[
				'AND'=>[
		//这里的优化建议 使用id  主键列进行搜索  key_len长度比 4(primary key):126(unique索引) ,没办法优化,得出id的代价和更新代价一样  
						'userId'=>$fromUserId,
						'messageId'=>$messageId
						]
				]);
				if($conditionCheck1!=0){ //返回值是0 就是没有更新
					//更新成功
					
					return true;
				}else{//更新失败
					throw new PDOException($pdo->last_query().'   来自updateDynamicCount---'.MESSAGE_SCHOOL_RELATIONSHIP.$schoolId);
				}
			}else{//如果是在用户表找不到学校id 那么就是更新完成 可以返回了
				return true;
			}  
			//注意这里 viewModel根据插件类返回的值,来判断是否事务回滚 所以如果无错误一定要返回true
			
		}catch(PDOException $exception){
			Tool::debug_content( $exception->getMessage() );
			return false;
		}

	}
}

?>