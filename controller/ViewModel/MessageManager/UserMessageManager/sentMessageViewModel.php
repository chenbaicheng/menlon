<?php
//
//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'ViewModel'.DIRECTORY_SEPARATOR.'viewModelBaseClass.php'; //class Tool

//引入消息 话题关系类<中间件> <负责创建表格 和更新话题表 话题归类> ViewModel 
require_once Tool::returnAbsoluteRouteString(['controller','ViewModel','MessageManager','MessageTitleManager','MessageTitleListViewModel.php'
]);

//引入学校分表类<中间件>   负责每个学校创建表和将用户的消息归类到所属学校  
require_once Tool::returnAbsoluteRouteString(['controller','ViewModel','MessageManager','SchoolManager','SchoolListViewModel.php']);

//引入网络层 负责推送 
require_once Tool::returnAbsoluteRouteString(['controller','NetWorkRequest','RongYun','commentSubmitToUser','CommentSubmit.php']);

class sentMessageViewModel extends  viewModelBaseClass{

	public function __construct($dbName){   
	    //$parent='viewModelBaseClass';
		return parent::__construct($dbName);
	}
	//****发送普通消息的成员
	private $userId,$insertArray,$idArray,$messageTitleArray; //这里的userid 不是uuid ,是 表前缀_所在行数 组成的id 
	//评论成员
	private $insertArray2,$ForUserId;
	//****发送回复消息的成员  不需要  直接使用 $insertArray 就可以 
/* 	private $replyId,$messageId,$Ualais,$content; */
	
	function sentMessageByUserId(&$pdo,$userId,&$insertArray,$messageTitleArray=''){ //pdo要传入 这个类生成的pdo
	//数组转换为对象,因为action函数需要使用这个,他只有一个实参,所以只能使用变量
		$this->userId=$userId;
		$this->insertArray =$insertArray;
		
		//这里会将 9_11 分割成[9,11]  表明在第9个表的 序号11的位置,注意因为存在事务回滚 ,auto_increment不会减回原样  
		$this->idArray = Tool::slice_userId_ReturnArray($this->userId);  
		
		$this->messageTitleArray = $messageTitleArray;
		//检查 这个话题是否已经被记录过,不能重复记录,否则Tid 会出错 
		if($this->messageTitleArray!=''){
			try{
				$datas = $pdo->select(MessageTitle,[
				'Tname'
				],[
				'Tname'=>$messageTitleArray['Tname']
				]);
				//检查是否存在
				if(!empty($datas)){
					//echo $pdo->last_query();
					return ['code'=>801,'error_msg'=>'话题已经存在'];
				}
			}catch(PDOException $exception){
				return '';
			}
	
		}
//这里已经设置了 事务等级 在medoo的PDO构造函数源码 中查找     
//返回值也请查看medoo.php文件 
		$actionReturnValue = $pdo->action(function(&$pdo){ 
				//检查表是否已经创建了
				try{ //注意没有$号的 都在db.config.php中定义了的常量
					//首先检查话题
					
					//初始化话题数据表 中间件对象 
					$messageTitleListViewModelobj = new MessageTitleListViewModel(DATABASE_NAME);
					$result=0;//这是初始化,如果有新话题插入,这个记录新话题所在行数,
					if($this->messageTitleArray!=''){
						//这里不为空 就证明 他是一个新的话题 
						//利用中间件 插入话题总表 
						//首先 要添加时间,因为字段里面有时间
						$this->messageTitleArray['TsubmitTime']=$this->insertArray['Time_t'];
						//然后插入
						$result=$messageTitleListViewModelobj->insertToTitleList($pdo,$this->messageTitleArray,$this->userId);
						// 插入了话题以后  更新insertArray['Tid']
						if($result!==false){
							$this->insertArray['Tid']=$result;//这是新插入话题的序号 
						}
						
						//创建属于这个话题 的 消息话题联系表     
						//输入数据库名字 SEARCH_DATABASE_NAME
						//在上面引入  MessageTitleListViewModel 类 不能在这里初始化,因为作用域是if 出了if 范围就会被销毁
						/* $messageTitleListViewModelobj = new MessageTitleListViewModel(DATABASE_NAME); */
					
						//因为功能要分开 所以只能新建一个类     
						//使用中间件 创建话题表 和话题索引
						$returnValue = $messageTitleListViewModelobj->createTableOnCreateNewTid($messageTitleListViewModelobj,$this->insertArray['Tid'],$this->userId); //$result 就是插入话题的编号 ,发出帖子的用户id --$userId 
						if($returnValue==''){
							throw new PDOException('创建关系表失败');
						}
					}
					
					//将发出的帖子 插入用户自己所在的消息表中 
					$messageId= $pdo->insert(Message_const.$this->userId,
					$this->insertArray
					);
			
					if(!$messageId){
						throw new PDOException('userId:'.$this->userId.'  '.$pdo->last_query());

					}
					
					//发布一条信息 要更新用户表的发布数量<注意 因为学校,话题共用同一个帖子(冗余记录),所以这里只要加1(不是加3),话题和学校表的自增,是用来统计此话题,和学校的发帖数量,和用户注册表中记录的发帖数量,意义是不一样的>
					$conditionCheck2=$pdo->update(UserInformation.$this->idArray[0],[
					'Msg_count[+]'=>1
					],[
					'User_id'=>$this->idArray[1]
					]);  //注意这里的 User_id 和userId<组合出来的标识符,userId包含了User_id > 是不一样的 
					
					if(!$conditionCheck2){
						throw new PDOException('userId:'.$this->userId.'  '.$pdo->last_query());
					}
					
					
					//开始处理话题表的插入与更新
					
					//在用户t_msg表插入帖子消息成功后 就要在消息话题联系 那里插入 帖子和话题的联系关系 
					//使用中间件 更新话题表 
					
					$insertToTitleTableArray = [
						'userId'=>$this->userId,
						'ForUalais'=>$this->insertArray['ForUalais'],
						'messageId'=>$messageId,	
						'Mcontent'=>$this->insertArray['Mcontent'],
						'Mhttp'=>$this->insertArray['Mhttp'],
						'extra'=>$this->insertArray['extra'],
						'dtime'=>TimeForChina
						];
					//利用中间件 插入所属话题表
					$returnValue = $messageTitleListViewModelobj->addMessageTitleListToDataTable($pdo,$this->insertArray['Tid'],$insertToTitleTableArray); //pdo要传入 负责事务处理的pdo
					if($returnValue===false){
						throw new PDOException('插入关系失败');

					}
					//利用中间件 更新所在话题的发帖数量,前面插入了一条新帖子  所以现在要更新 
					//function updateTitleMessageCount($tid,$userId);
					$returnValue = $messageTitleListViewModelobj->updateTitleMessageCount($pdo,$this->insertArray['Tid'],$this->userId);
					
					// end 插入/更新话题表 
					
					//*************************************************
					//begin  学校表 的创建与消息插入 
					//检查此用户的学校编号 学校名称,如果存在就创建学校表,并且将这条消息插入此学校分类  
					//因为此用户的学校是那个 就插入那个学校表 
					$conditionCheck1=$pdo->select(UserInformation.$this->idArray[0],[
					'UschoolIndex',
					'Uschool'
					],[
					'User_id'=>$this->idArray[1]
					]);
					if(!empty($conditionCheck1)){
						//将学校id和学校名称 插入学校总表
						//这里只插入schoolid  
						$schoolId ='';
						$schoolName='';
						foreach ($conditionCheck1 as $data){
							$schoolId =$data['UschoolIndex'];
							$schoolName = $data['Uschool'];
						}
						//得出学校编号  ,然后使用拓展类 创建表格
						//初始化数据库中间件
						$schoolListViewModelobj = new SchoolListViewModel(DATABASE_NAME);
						//利用中间件 插入学校总表 
						$conditionCheck1 = $schoolListViewModelobj->insertToSchoolList($pdo,$schoolId,$schoolName);
						if($conditionCheck1!==true){
							throw new PDOException('检索 或者插入学校表的时候 发生错误 --sentMessageViewModel,SchoolListViewModel');
						}
						//利用中间件创建表格 
						$conditionCheck1= $schoolListViewModelobj->createTableOnCreateNewSchoolIndex(
								$schoolListViewModelobj,$schoolId);
								
						if($conditionCheck1===false){
							throw new PDOException('创建school表格失败 来自sentMessageViewModel');
						}
						//利用中间件 插入数据 
						$insertTableName = MESSAGE_SCHOOL_RELATIONSHIP.$schoolId; //以学校id分组 
						$insertArray3 = [
							'userId'=>$this->userId,
							'ForUalais'=>$this->insertArray['ForUalais'],
							'messageId'=>$messageId,/*这里的messageId是前面插入原创帖子后 返回的行数 就是messageId*/
							'Mcontent'=>$this->insertArray['Mcontent'],
							'Mhttp'=>$this->insertArray['Mhttp'],
							'extra'=>$this->insertArray['extra'],
							'dtime'=>TimeForChina
							];
						$insertErrorContent = '将帖子插入学校_消息分表中发生错误';
						$conditionCheck1= $schoolListViewModelobj->insertMessageToSchoolTable(
								$pdo,$insertTableName,$insertArray3,$insertErrorContent
								);
						if($conditionCheck1===false){
							throw new PDOException('创建school表格失败 来自sentMessageViewModel');
						}
						//发布一条信息 需要更新此用户所在学校表的发帖总数 
						//利用中间件 更新此用户所在学校表的发贴数量
						//function updateSchoolTitleMessageCount(&$pdo,$schoolId,$userId);
						$conditionCheck1= $schoolListViewModelobj->updateSchoolTitleMessageCount(
								$pdo,$schoolId,$this->userId
								);
						if($conditionCheck1===false){
							throw new PDOException('创建school表格失败 来自sentMessageViewModel');
						}
					}
					
					
					
					//发布一条信息 要更新用户表的发布数量,同时也要更新话题表中 话题发布的数量 
					//这里如果使用 $messageTitleListViewModelobj  一个新建的pdo链接 会发生死锁...注意这里使用了事务action()函数, 这个只能用在同一个pdo链接上面 ,现在这里已经有两个pdo链接了<事务的pdo是$pdo ,但是下面用$messageTitleListViewModelobj ,这里两个pdo是不同的,就算他们是链接同一个数据库> ,事务回滚是没有办法 处理两个pdo链接
/* 					$conditionCheck2=$messageTitleListViewModelobj->update(MessageTitle,[
					'Tnum[+]'=>1
					],[
					'Tid'=>$this->insertArray['Tid']
					]);
					if(!$conditionCheck2){
						throw new PDOException('userId:'.$this->userId.'  '.$pdo->last_query());
					} */
					
					
					  
					
					
				}catch(PDOException $exception){
					
					Tool::debug_content( $exception->getMessage() );
					return false;
				}
		}); //end action
		
	//@imoment  这里修改了medoo的源码  错误返回值为 -1,正确为0      
			if($actionReturnValue!= -1){
				return ['code'=>200,'userId'=>$this->userId];
				//这里返回了用户的表名后缀  
				//这个是submit函数的返回值 
			}else{ //没有写入成功的 事务回滚的  
				return '';
			}
    }//end class function
	
	//*****  评论的数据库操作  
	function sentReplyMessage(&$pdo,$userId,&$insertArray,&$insertArray2){  //注意这里pdo是不一样的  
	
		$this->userId=$userId;
		$this->insertArray =$insertArray; 
		//这里会将 9_11 分割成[9,11]  表明在第9个表的 序号11的位置,注意因为存在事务回滚 ,auto_increment不会减回原样  
		//$this->idArray = Tool::slice_userId_ReturnArray($this->userId);  
		$this->insertArray2= $insertArray2;
		$this->ForUserId=$insertArray['ForUserId'];
		
//这里已经设置了 事务等级 在medoo的PDO构造函数源码 中查找     
//返回值也请查看medoo.php文件 
		$actionReturnValue = $pdo->action(function($pdo){ 
				//检查表是否已经创建了
				try{ //注意没有$号的 都在db.config.php中定义了的常量

				//B对A发出评论 , 评论记录在A身上 ,B只保留他的评论内容,A消息id,用户A id    			
////第一个数组 插入 replymsg_msg_A  第二个插入 replayMessageTo_B --B评论了什么记录在这里

					$conditionCheck1=$pdo->insert(ReplyMsg_Msg.$this->userId,$this->insertArray);
					if(!$conditionCheck1){
						throw new PDOException('userId:'.$this->userId.'  '.$pdo->last_query());

					}
					//这里将insert函数的返回值 push入insertArray2
					//因为他在 msg_msg 插入的行, 就是评论的id ,如果B用户要在A找到他评论过的信息,需要A用户id,消息Id,评论Id
					/* array_push($this->insertArray2,['replyId'=>$conditionCheck1]); //array_push () 导致错误  */
					$this->insertArray2['replyId']=$conditionCheck1;
					
					$conditionCheck2=$pdo->insert(ReplyMessageTo.$this->ForUserId,$this->insertArray2);
				
					if(!$conditionCheck2){
						
						throw new PDOException('userId:'.$this->userId.'  '.$pdo->last_query() );

					} 
					
					//融云推送  
					// 使用网络层 推送消息  
	
					$returnValue = CommentSubmit::commentSubmitToRongYun($this->insertArray['ForUserId'],[$this->insertArray['AtUserId']],[
					'replyId'=>$this->insertArray2['replyId'],
					'ForUserId'=>$this->insertArray['ForUserId'],
					'AtUserId'=>$this->insertArray['AtUserId'],
					'ForUalais'=>$this->insertArray['ForUalais'],
					'AtUserName'=>$this->insertArray['AtUserName'],
					'replyContent'=>$this->insertArray['replyContent'],
					'replyDateTime'=>$this->insertArray['replyDateTime']
					]);
					
					if($returnValue!=true){
						throw new PDOException( $returnValue);
					}
				}catch(PDOException $exception){
					$arraytmp= $exception->getMessage();
					if(is_array($arraytmp)){
						return $arraytmp;
					}
					Tool::debug_content( $exception->getMessage() );
					return false;
				}
		}); //end action
	//@imoment  这里修改了medoo的源码  错误返回值为 -1,正确为0      
			if($actionReturnValue!= -1){
				return ['code'=>200,'userId'=>$this->userId];
				//这里返回了用户的表名后缀  
				//这个是submit函数的返回值 
			}else{ //没有写入成功的 事务回滚的  
				return '';
			}	
	}//end class function
}

?>