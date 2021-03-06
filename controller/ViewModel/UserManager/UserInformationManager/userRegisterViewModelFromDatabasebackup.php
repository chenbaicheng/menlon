<?php
//
//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'ViewModel'.DIRECTORY_SEPARATOR.'viewModelBaseClass.php'; 

require_once Tool::returnAbsoluteRouteString(['controller','ViewModel','UserManager','UserInformationManager','UalaisIndex','UalaisIndex.php']);
// 引入初始化用户数据库的类  
/* require_once Tool::returnAbsoluteRouteString(['model','databaseAccessProxy_Model','object_initDatabaseCreateProxy_model.php']); */



class userRegisterViewModelFromDatabase extends  viewModelBaseClass{

	public function __construct($dbName){    
	    $parent='medoo_returnPDO';//这里不要使用ViewModel基类 因为那时候还没有userId,token
      
		return $parent::__construct($dbName);
	}

	public function checkUserPhoneIsExist($UPhone,&$pdo,$tableName){
		
		try{
		$datas= $pdo->select($tableName,[
		'UPhone'
		],[
		'UPhone'=>$UPhone
		]);
		
		return $datas; 
		}catch(PDOException $e){
			
			return [];
		}
	
	}
	

	
	private $table_name,$UPhone,$checkNum,$insertArray,$userTableID;
	function register($table_name,$UPhone,$checkNum,&$pdo,&$insertArray){ //pdo要传入 这个类生成的pdo
		$this->table_name =$table_name ;
		$this->checkNum = $checkNum;
		$this->UPhone = $UPhone;
		$this->insertArray = $insertArray;
		//问题1: 如果先检查缓冲表--loginbuff  步骤1  如果在这里找不到用户 就会返回,这样没有办法检查已经存在的用户 
		//---如果先检查 用户注册表 然后再检查 缓冲表--loginbuff  
		//begin  这里需要检查 缓冲表--loginbuff 是否存在这个用户的信息 ***********    
		
		
		try{
			//检查注册表  ---假设 用户没有注册,那么他应该在验证码缓冲表记录,如果有注册 ,他会记录在注册表,所以应该先
			//检查用户表 ,再检查验证码缓冲表 ,---防止重复插入 和注册  
			// 现在先检查 用户表 如果没有,那么就代表他是新用户,(注意这里 因为手机能保证唯一性,就是同一个手机不会出现在一个地方,在发验证码的时候已经检查了 用户注册表和缓冲表的用户,才开始发送验证码的,   )
			
			//**********前提 : 1.用户如果重复插入 会提示错误  2.如果插入一个新用户 需要核对缓冲表中有没有存在这个用户 , 这时候需不需要查找他是不是已经在数据库中呢,如果需要查找,应该在发送验证码的时候就要检查了 对吗?
			//  对的, 所以在插入的时候不需要检查用户表本身是否已经存在这个用户,---前面在发送验证码之前 ,要检查用户表不存在, 注册写入时候,要检查缓冲表存在这个用户,
		
			//end 用户表检查-使用手机号码  
			/* ****************
			用户注册插入 必须要在缓冲表找到对应的 验证码记录 才能插入 
			*********************** */
			//检查验证码缓冲表  
			$datas = $pdo->select('loginbuff',[
			'UPhone'
			],[
			'AND'=>[
				'UPhone'=>$this->UPhone,
				'checkNumber'=>$this->checkNum
				]
			]);
			if( empty($datas) ){ //如果在缓冲表找不到,就说明他没有提交验证 所以不予注册写入  
				throw new PDOException('UPhoneNotInLoginBuff');
			}
		}catch(PDOException $exception){
			
			if($exception->getMessage()=='UPhoneNotInLoginBuff'){
				return ['code'=>801,'error_msg'=>'请输入正确的验证码和手机号'];
			}
			//剩下的错误才要记录在文件中 
			Tool::debug_content( $exception->getMessage(),'检查loginBuff出现的错误' );
			return '';//无知原因的错误输出
		}
		//********end  
		
		//注意 字段名  int 类型的 不能为null 一定要设置 默认值
		//$pdo->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		try{
				$conditionCheck_0=$pdo->query('CREATE TABLE IF NOT EXISTS  '.$this->table_name.'(
				User_id int not null auto_increment,
				uuid varchar(120),
				User_name varchar(20),
				Msg_count int default 0,
				Fans_count int default 0,
				Follow_count int default 0,
				Ualais varchar(20) not null ,
				UimageSrc varchar(150) null ,
				UEmail varchar(20) null unique,
				UPhone varchar(20) not null unique,
				Upasswd varchar(160) not null,
				Usex tinyint null,
				UschoolIndex int null,
				Uschool varchar(40) null,
				Uskin tinyint null,
				Utel varchar(15)  null,
				Uinfo varchar(240)  null,
				Udatetime Datetime  not null,
				primary key (User_id) 
				);');//这里主键设为 邮箱和自增id 
				if(!$conditionCheck_0){
					throw new PDOException('创建注册表失败');
				}else{
					$message_static_file = 'registerTmp'.DIRECTORY_SEPARATOR.$this->table_name.'.json'; //将数组保存成json  静态化 
					
					if(!file_exists($message_static_file)){ //如果存在文件就说明索引已经建立					
					//创建索引  , 记得select 语句where条件一定要是索引 否则会出现死锁
						$conditionCheck_0 =$pdo->query('create index '.$this->table_name.'Index'.' 
										on '.$this->table_name.'(
										UschoolIndex
										);create index '.$this->table_name.'Index2'.' 
										on '.$this->table_name.'(
										Fans_count DESC,Msg_count DESC
										);');//注意在调试的时候,记得删除索引文件  
						if(!$conditionCheck_0){
							throw new PDOException('创建索引 注册表失败');
						}
						file_put_contents($message_static_file,JSON_Array_Control::array_to_json(['code'=>200,'content'=>'索引创建成功']) );//成功创建索引后,写入内容到对应静态文件中
						
					}
				

				}

		}catch(PDOException $exception){
			//这里是预防语法错误 
			Tool::debug_content( $exception->getMessage() );

			return '';
		}

	//这里使用了引用 
		$actionReturnValue = $pdo->action(function(&$pdo){
				//检查表是否已经创建了
				//下面两个变量 都需要在catch中使用 
				$id = UalaisIndex::getHashTableIdByUalais($this->insertArray['Ualais']);
				$conditionCheck_1=0;
				try{
   
				//优化建议 这里不放入事务,只讲判断条件放在事务,然后出错了,就设置标记位,以后需要使用先找这个缓冲池.....或者干脆将失败后的序号放进缓冲池判断		
					 $conditionCheck_1= $pdo->insert($this->table_name,$this->insertArray);
					//$conditionCheck_1 是插入成功后返回的序号 
					
					
					if(!$conditionCheck_1){
						
						throw new PDOException('插入注册用户数据失败 来自userRegisterViewModel');
					}else{
						$this->userTableID = Tool::get_TableId($this->insertArray['uuid']).'_'.$conditionCheck_1;// $conditionCheck_1 是前面插入的行数  
						
								//引入UalaisIndex中间件  负责创建用户昵称索引表 和插入索引信息
								$conditionCheck1=UalaisIndex::createTableAndIndex($pdo,$this->insertArray['Ualais']);
								if($conditionCheck1===false){
									throw new PDOException('创建用户昵称索引错误 来自userRegisterViewModel');
								}
								
								//static function insertToUalaisIndex(&$pdo,$insertTableName,&$insertArray);
								$insertArrayTmp=[
								'Ualais'=>$this->insertArray['Ualais'],
								'userId'=>$this->userTableID,/*这是前面组合起来的 分别代表表号和行数*/
								'UimageSrc'=>$this->insertArray['UimageSrc']
								];
								$conditionCheck1=UalaisIndex::insertToUalaisIndex($pdo,$id.UalaisIndexTableName,$insertArrayTmp);
								if($conditionCheck1===false){
									throw new PDOException('插入用户昵称索引错误 来自userRegisterViewModel');
								}
								//end
								 
								
								//create table
								//*************************end
								/*1. 收藏fav_MessageFrom_somebody  表(就是点赞)： 注意表名之间不能留空格*/
								 //todo 	
								// echo '来自Proxy_Model'.Favour_const.$this->userTableID.'<br />';
								 
								$conditionCheck1= $pdo->query('CREATE TABLE IF NOT EXISTS '.Favour_const.$this->userTableID.'(
								Fid int auto_increment not null,
								UserId varchar(40) not null,
								Messageid int not null,
								Ualais varchar(20) not null,
								commitTime datetime not null default now(),
								primary key(Fid),
								CONSTRAINT '.Favour_const.$this->userTableID.'_unique'.' UNIQUE (UserId,MessageId)
								); ');  /*这里设置unique 是防止多条同样的信息插入 */
								
								if(!$conditionCheck1){
									
									throw new PDOException($pdo->last_query());
								}
								//这个是 运行完 就立即检查    创建索引
								$conditionCheck_step_by_step= $pdo->query('create index '.Favour_const.$this->userTableID.'Index'.' on '.Favour_const.$this->userTableID.'(
								commitTime
								)');
								if(!$conditionCheck_step_by_step){
									throw new PDOException('创建 fav 表索引失败');
								}
								 //end     

							/*2. 关注表(  用户之间联系表(t_user_relation) --必须有关注与被关注的关系  ):*/
								 //todo 
								$conditionCheck2 = $pdo->query('CREATE TABLE IF NOT EXISTS '.Relation_const.$this->userTableID.'(
								id int not null auto_increment,
								toUserId varchar(40) not null ,
								toUalais varchar(20) not null,
								Type tinyint not null,
								GroupName varchar(20),
								primary key (id),
								CONSTRAINT '.Relation_const.$this->userTableID.'unique'.'  UNIQUE(Type,toUserId,GroupName)
								); 
								');  /* CONSTRAINT uc_PersonID UNIQUE (Id_P,LastName) */
								//创建索引  
								if(!$conditionCheck2){
									throw new PDOException($pdo->last_query());
								}
								//这里暂时不需要索引 前面unique已经创建了需要的索引
								/* $conditionCheck_step_by_step= $pdo->query('create index '.Relation_const.$this->userTableID.'Index'.' on '.Relation_const.$this->userTableID.'(
								GroupName,Type
								)');
								if(!$conditionCheck_step_by_step){
									throw new PDOException('创建 relation 表索引失败');
								} */
								
								//end
								
								 //下面是正常生成的  
								 /*
								 4. 用户使用标签UserLabel_somebody
								 这个考虑是不是 全部用户使用同一张表格 
								 */
								$conditionCheck3 =	$pdo->query('CREATE TABLE '.UserLable_const.$this->userTableID.'(
								id  int  not null auto_increment,
								Lid  int not null,
								primary key(id),
								CONSTRAINT '.UserLable_const.$this->userTableID.'unique'.'  UNIQUE(Lid)
								);');  /*注意这里的 表名变量 都是有规定命名规则 看文档 */
								
								if(!$conditionCheck3){
									throw new PDOException($pdo->last_query());
								}
								//这个不需要索引
								/* $conditionCheck_step_by_step= $pdo->query('create index '.UserLable_const.$this->userTableID.'Index'.' on '.UserLable_const.$this->userTableID.'(
								id,Lid
								)');
								if(!$conditionCheck_step_by_step){
									throw new PDOException('创建 relation 表索引失败');
								} */
								
								 //*****************************************end 

								/* 1.普通消息message表<  t_msg_info  >：*/
								 ///******************  todo    
								 // 声明 Mid 为了保证每个用户之间都是独立的  所以限定消息编号格式为 $username_序号(序号来源 t_user_info_** Msg_count 的大小   ) 
								//这里的userTableID 和User_id 是同一个字段,需要User_id字段只是为了方便找到这张表,所以不用为User_id建立索引   
								 $conditionCheck4 = $pdo->query("CREATE TABLE IF NOT EXISTS ".Message_const.$this->userTableID."(
											Msg_id int not null auto_increment  ,
											User_id varchar(40) not null, 
											ForUalais varchar(20) not null,
											Tid int  null ,
											Cid int null,
											Mcontent varchar(150) not null,
											Mhttp varchar(150) null, 
											ImgeWidthAndHeight_JSON varchar(32) ,
											location_x double null,
											location_y double null,
											locationName varchar(100) null,
											extra varchar(512) null,
											Type tinyint not null, 
											Mfav   int   null default 0,
											Commented_count int  null default 0,
											Transferred_count int  null default 0, 
											Time_t datetime not null ,
											primary key (Msg_id)
											);");
								
								
								if(!$conditionCheck4){
									throw new PDOException($pdo->last_query());
								}
							//创建索引
								/* $conditionCheck_step_by_step= $pdo->query('create index '.Message_const.$this->userTableID.'Index'.' on '.Message_const.$this->userTableID.'(
								Time_t DESC,Commented_count DESC,Transferred_count DESC,Mfav DESC
								);'); */
								$conditionCheck_step_by_step= $pdo->query('create index '.Message_const.$this->userTableID.'Index'.' on '.Message_const.$this->userTableID.'(
								Time_t DESC
								);');/*因为只用到时间排序*/
								if(!$conditionCheck_step_by_step){
									throw new PDOException('创建 relation 表索引失败');
								}
								 //end
								/*6. 皮肤skin_somebody表： 这个不用分表 */
								//begin
								
								$conditionCheck5 = $pdo->query("CREATE TABLE IF NOT EXISTS ".Skin_const.$this->userTableID."(
								Sid int auto_increment primary key not null,
								SimageSrc varchar(150) not null ,
								Scolor varchar(20) not null   
								);");  
								
								if(!$conditionCheck5){
									throw new PDOException($pdo->last_query());
								}
								//end
								$conditionCheck_step_by_step= $pdo->query('create index '.Skin_const.$this->userTableID.'Index'.' on '.Skin_const.$this->userTableID.'(
								Sid
								)');
								if(!$conditionCheck_step_by_step){
									throw new PDOException('创建 skin 表索引失败');
								}
																//**********评论表
								//这里建立一个 用户评论信息表（replyMessageTo_someone）
								$conditionCheck6 = $pdo->query('CREATE TABLE IF NOT EXISTS '.ReplyMessageTo.$this->userTableID.'(
								id  int auto_increment,
								replyid int null,   
								userId varchar(40) not null,
								replyContent varchar(200) not null,
								messageId int not null,
								ForUalais varchar(20),
								replyDateTime datetime,
								primary key(id)
								);
								');  /*这里 只有id 和时间需要索引 */
								
								if(!$conditionCheck6){
									throw new PDOException($pdo->last_query());
								}
								
								$conditionCheck_step_by_step= $pdo->query('create index '.ReplyMessageTo.$this->userTableID.'Index'.' on '.ReplyMessageTo.$this->userTableID.'(
								id,replyDateTime ASC
								)');
								if(!$conditionCheck_step_by_step){
									throw new PDOException('创建 reply 表索引失败');
								}
								
								

								//5.评论消息与消息 表(replymsg_msg_B)  add: //B对A发出评论 , 评论记录在A身上 ,B只保留他的评论内容,A消息id,用户A id   ***
								$conditionCheck7 = $pdo->query('CREATE TABLE IF NOT EXISTS '.ReplyMsg_Msg.$this->userTableID.'(
								replyId int auto_increment not null,
								userId  varchar(40) not null,
								messageId  int not null,
								
								ForUalais varchar(20) not null,
								ForUserId varchar(40) not null,
							
								AtUserId varchar(40)  null,
								AtUserName varchar(20)  null,
								dependReplyId int default 0,
								replyContent varchar(200) not null,
								replyDateTime datetime,
								primary key(replyId)
								);');
								
								if(!$conditionCheck7){
									throw new PDOException($pdo->last_query());
								}
								$conditionCheck_step_by_step= $pdo->query('create index '.ReplyMsg_Msg.$this->userTableID.'Index'.' on '.ReplyMsg_Msg.$this->userTableID.'(
								messageId,dependReplyId,replyDateTime ASC
								)');
								if(!$conditionCheck_step_by_step){
									throw new PDOException('创建 relation 表索引失败');
								}
								
								//end*****************
							//这里创建所有表后 删除缓冲表的内容 
								$pdo->delete('loginbuff',
								[
								'UPhone'=>$this->UPhone
								]);
						
					}
	
				}catch(PDOException $exception){
					//这里好像出了点问题 ,$conditionCheck1 之前的不会回滚  
					//所以在这里需要手动删除 用户表的数据
				 	$pdo->delete($this->table_name,[
					'User_id'=>$conditionCheck_1
					]); 
					//删除数据表
					$pdo->query('
					drop table if exists '.$id.UalaisIndexTableName.';
					drop table if exists '.Favour_const.$this->userTableID.';
					drop table if exists '.Relation_const.$this->userTableID.';
					drop table if exists '.UserLable_const.$this->userTableID.';
					drop table if exists '.Message_const.$this->userTableID.';
					drop table if exists '.Skin_const.$this->userTableID.';
					drop table if exists '.ReplyMessageTo.$this->userTableID.';
					drop table if exists '.ReplyMsg_Msg.$this->userTableID.';
					');
					Tool::debug_content( $exception->getMessage() );
					return false;
				}
				
		}); //end action
	//@imoment  这里修改了medoo的源码  错误返回值为 -1,正确为0
			if($actionReturnValue!= -1){
				
				return ['code'=>200,'userId'=>$this->userTableID];
				//这里返回了用户的表名后缀  
				//这个是submit函数的返回值 
			}else{ //没有写入成功的 事务回滚的  
				return '';
			}
			
	

    }
}

?>