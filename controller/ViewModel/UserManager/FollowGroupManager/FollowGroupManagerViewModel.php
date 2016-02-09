<?php

//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'ViewModel'.DIRECTORY_SEPARATOR.'viewModelBaseClass.php'; //class Tool


class FollowGroupManagerViewModel extends  viewModelBaseClass{

	public function __construct($dbName){    
	   // $parent='viewModelBaseClass';
		
		return parent::__construct($dbName);
	}
	
	//只能针对一个用户 
	private $forUserId,$toUserId,$groupName; //editFollowGroup()      
	private $insertArray,$insertArray2; //addUser()      
	//删除和添加是同一个函数,在于参数不同 
	public function editFollowGroup($pdo,$forUserId,$toUserId,$groupName=''){
		//这里的添加 其实只是修改数据库的字段   GroupName
		
		$this->forUserId = $forUserId;
		$this->toUserId =$toUserId;
		$this->groupName =$groupName; 
		$actionReturnValue = $pdo->action(function($pdo){ 
				//检查表是否已经创建了
				try{ //注意没有$号的 都在db.config.php中定义了的常量  
					$conditionCheck1 = $pdo->update(Relation_const.$this->forUserId,[
					'GroupName'=>$this->groupName
					],[
					'toUserId'=>$this->toUserId
					]);
					
					if(!$conditionCheck1){
						throw new PDOException('更新注册用户数据失败');
					}
					
				}catch(PDOException $exception){
					
					Tool::debug_content( $exception->getMessage() );
					return false;
				}
		}); //end action  transaction 
	//@imoment  这里修改了medoo的源码  错误返回值为 -1,正确为0
			if($actionReturnValue!= -1){
				return ['code'=>200,'forUserId'=>$this->forUserId];
				//这里返回了用户的表名后缀  
				
			}else{ //没有写入成功的 事务回滚的  
				return '';
			}
	}
	
	//只能针对一个用户  ,删除组名其实和添加是一样的,删除只是还原成默认值   
/* 	public function deleteFollowGroup(){
		//这里的删除 也是将GroupName 还原 成默认值
		
	} */
	
	//添加用户,可以有两种模式,默认值,和指定分组  forUserId
	public function addUser($pdo,$forUserId,$toUserId,&$insertArray,&$insertArray2){
		$this->toUserId = $toUserId;
		$this->forUserId = $forUserId;
		$this->insertArray = $insertArray;/*因为当A用户点击了关注B的按钮, insertArray是插入A表,标识他关注了谁,insertArray2是插入B表,让B知道谁关注了他,也方便发送推送信息,如果B发送了一些新消息,那么根据type =0 找出所有用户,然后根据userId发送推送 */
		$this->insertArray2 = $insertArray2; 
		//首先检查forUserId 和 toUserId 是否存在....这个好像不需要,这个可以人为保证正确,如果是仿冒,可以考虑对称加密,保障正确 
		// 因为搜索 表需要很长时间, 社交软件这个一致性可以忽略  
		$actionReturnValue = $pdo->action(function($pdo){ 
				//检查表是否已经创建了
				try{ //注意没有$号的 都在db.config.php中定义了的常量
					//这个是插入forUser 关注了toUser
					$conditionCheck1 = $pdo->insert(Relation_const.$this->forUserId,$this->insertArray);					
					//echo $pdo->last_query();   
					if(!$conditionCheck1){
						throw new PDOException('插入关注用户数据失败 来自FollowGroupManager:'.$pdo->last_query());
					}
					//这个是toUser 被forUser关注了(粉丝数据)   
					$conditionCheck1 = $pdo->insert(Relation_const.$this->toUserId,$this->insertArray2);
					if(!$conditionCheck1){
						throw new PDOException('插入粉丝用户数据失败 来自FollowGroupManager:'.$pdo->last_query());
					}
					//最后更新用户的关注数据 toUser,forUser 都要更新用户信息表的Fans_count Follow_count 字段
					//1.先将 userId分割  
					//这里会将 9_11 分割成[9,11]  表明在第9个表的 序号11的位置,注意因为存在事务回滚 ,auto_increment不会减回原样  
					//$insertArray 的toUserId 是 被关注的用户id
					$idArray = Tool::slice_userId_ReturnArray($this->insertArray['toUserId']);  
					//$insertArray2 的toUserId 是 点击关注按钮的用户id
					$idArray2 = Tool::slice_userId_ReturnArray($this->insertArray2['toUserId']);
					//更新被关注者的 粉丝数量
					$conditionCheck1=$pdo->update(UserInformation.$idArray[0],[
					'Fans_count[+]'=>1
					],[
					'User_id'=>$idArray[1]
					]);
					if(!$conditionCheck1){
						throw new PDOException('增加粉丝用户数据失败 来自FollowGroupManager:'.$pdo->last_query());
					}
					//更新 点击关注按钮的用户 的 他关注人数Follow_count  
					$conditionCheck1=$pdo->update(UserInformation.$idArray2[0],[
					'Follow_count[+]'=>1
					],[
					'User_id'=>$idArray[1]
					]);
					if(!$conditionCheck1){
						throw new PDOException('增加关注用户数据失败 来自FollowGroupManager:'.$pdo->last_query());
					}
				}catch(PDOException $exception){
					
					Tool::debug_content( $exception->getMessage() );
					return false;
				}
		}); //end action  transaction 
	//@imoment  这里修改了medoo的源码  错误返回值为 -1,正确为0
			if($actionReturnValue!= -1){
				return ['code'=>200,'toUserId'=>$this->toUserId];
				//这里返回了用户的表名后缀  
				
			}else{ //没有写入成功的 事务回滚的  
				return '';
			}
	}
	
	//删除用户 
	public function deleteUser($pdo,$forUserId,$toUserId){
		$this->forUserId = $forUserId;
		$this->toUserId = $toUserId;
		$actionReturnValue = $pdo->action(function($pdo){ 
				//检查表是否已经创建了
				try{ //注意没有$号的 都在db.config.php中定义了的常量  
					//删除关注,这里是删除自己本身的关注,还需要删除toUserId的 
					$conditionCheck1 = $pdo->delete(Relation_const.$this->forUserId,[
					'toUserId'=>$this->toUserId
					]);					
					//echo $pdo->last_query();

					if(!$conditionCheck1){
						throw new PDOException('更新关注A用户数据失败,或者userId用户不存在');
					}
					//删除toUserId 用户 所在表的粉丝关注   
					$conditionCheck1 = $pdo->delete(Relation_const.$this->toUserId,[
					'toUserId'=>$this->forUserId
					]);					
					//echo $pdo->last_query();

					if(!$conditionCheck1){
						throw new PDOException('插入关注B用户数据失败,或者userId用户不存在');
					}
					
					//这里考虑是否删除 用户表中 粉丝数量 和关注数量  ,现在不删除
					//$insertArray 的toUserId 是 被关注的用户id
					$idArray = Tool::slice_userId_ReturnArray($this->toUserId);  
					//$insertArray2 的toUserId 是 点击关注按钮的用户id
					$idArray2 = Tool::slice_userId_ReturnArray($this->forUserId);
					//更新被关注者的 粉丝数量
					$conditionCheck1=$pdo->update(UserInformation.$idArray[0],[
					'Fans_count[-]'=>1
					],[
					'User_id'=>$idArray[1]
					]);
					if(!$conditionCheck1){
						throw new PDOException('增加粉丝用户数据失败 来自FollowGroupManager:'.$pdo->last_query());
					}
					//更新 点击关注按钮的用户 的 他关注人数Follow_count  
					$conditionCheck1=$pdo->update(UserInformation.$idArray2[0],[
					'Follow_count[-]'=>1
					],[
					'User_id'=>$idArray[1]
					]);
					if(!$conditionCheck1){
						throw new PDOException('增加关注用户数据失败 来自FollowGroupManager:'.$pdo->last_query());
					}
					
				}catch(PDOException $exception){
					
					Tool::debug_content( $exception->getMessage() );
					return false;
				}
		}); //end action  transaction 
	//@imoment  这里修改了medoo的源码  错误返回值为 -1,正确为0
			if($actionReturnValue!= -1){
				return ['code'=>200,'forUserId'=>$this->forUserId];
				//这里返回了用户的表名后缀  
				
			}else{ //没有写入成功的 事务回滚的  
				return '';
			}
	}
	
	function getUserList($pdo,$forUserId){
		$datas=$pdo->select(Relation_const.$this->forUserId,'*');
		if(!empty($datas)){
			return $datas;
		}else{
			return '';
		}
	}

}

?>