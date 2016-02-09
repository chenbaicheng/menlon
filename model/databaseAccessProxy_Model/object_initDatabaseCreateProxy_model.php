<?php
//这里不写入与数据库有关的代码
//***** 分离出  、ViewModel 层、Service 层、Storage 层
//*****  现在是 ObjectModel层  
//*****
//***************
/*
这里是数据访问层的  这个类负责建立 多张只属于这个用户的表
每个用户拥有的表是相互独立的   
还有每个用户拥有一个数据库
  
*/
require_once('checkDatabase_BaseClass.php');
class object_initDatabaseCreateProxy_model  extends  checkDatabase_BaseClass{
	private $database;  //PDO() return value  下面创建数据库表 使用这个操作数据库
    protected $username;
	public function __construct(){  
    //pdo have problem  , see this  http://stackoverflow.com/questions/4361459/php-pdo-charset-set-names
		$parentClassName='medoo_returnPDO';
	//这里使用 medoo_returnPDO 类的构造函数 
		return $parentClassName::__construct(DATABASE_NAME);  //链接 数据库 'imoment' 在配置文件中配置  

}
	public function __destruct(){
    // ... do not need release $database ,auto release
	
	}
	public  function check_user_table_isexit_then_create($username,&$pdo){ //因为是根据用户名表分割表的 
				//check username isempty?
			if($username==''){
				return '{"error":800}'; //用户名没有传入
				
			}else {  //这里不用检查数据表 因为用户id是唯一的 所以数据库表也是唯一的  
				//create pdo 
				$returnValue = $this->create_initial_objectTable($username,$pdo);
				//destroy pdo 	
				return $returnValue;		
							
			}
		return '{"error":801}'; //未知原因 
	}
	
    public function create_initial_objectDatabase($username,$pdo){  
	   //**********************
	   //
	   // 这是每个用户一个数据库的模式    ,现在不是采用这种模式所以忽略 
	   // 不用为每个用户创建一个数据库  
	   
	}
	

	
    private function create_initial_objectTable($username,$pdo){
        $this->username = $username ;
		$result_creatTable= $pdo->action(function($pdo){
				try{	
							//create table
							//*************************end
							/*1. 收藏fav_ to _somebody  表(就是点赞)：*/
							 //todo 	
							 echo '来自Proxy_Model'.Favour_const.$this->username.'<br />';
							 
							$conditionCheck1= $pdo->query('create table '.Favour_const.$this->username.'(
							Fid int auto_increment not null,
							Uid varchar(40) not null,
							Mid int not null,
							primary key(Fid,Uid,Mid) 
							);
							');  
							 
							 //end 

						/*2. 关注表(  用户之间联系表(t_user_relation) --必须有关注与被关注的关系  ):*/
							 //todo 
							$conditionCheck2 = $pdo->query("create table ".Relation_const.$this->username."(
							User_id  varchar(40) not null,
							Follow_id varchar(40) not null,
							Type tinyint not null,
							GroupName varchar(20),
							primary key (User_id,Follow_id) 
							); 
							");  /* */
							 //下面是正常生成的  
							 /*
							 4. 用户使用标签UserLabel_somebody
						属性名称	数据类型	属性说明
						Uid	Char(11)	用户编号
						Lid	char(11)	标签编号
							 */
							 $conditionCheck3 =	$pdo->query('CREATE TABLE '.UserLable_const.$this->username."(
						Uid  int  not null,
						Lid  int not null,
						primary key(Uid,Lid)
						);");  /*注意这里的 表名变量 都是有规定命名规则 看文档 
						 这里因为分表了  所以外键约束无法使用  
						CONSTRAINT fk_UserLabel_Uid_".strtolower($username)." foreign key(Uid) references  user_".strtolower($username)."(Uid),
						CONSTRAINT fk_UserLabel_Lid_".strtolower($username)." foreign key(Lid) references  lables(Lid) 
						*/
							 //*****************************************end 

							/* 1.普通消息message表<  t_msg_info  >：*/
							 ///******************  todo    
							 // 声明 Mid 为了保证每个用户之间都是独立的  所以限定消息编号格式为 $username_序号(序号来源 t_user_info_** Msg_count 的大小   )    
							 $conditionCheck4 = $pdo->query("create table ".Message_const.$this->username."(
										Msg_id int not null auto_increment  ,
										User_id varchar(40) not null, 
										Tid Char(16)  null ,
										Mcontent varchar(150) not null,
										Mhttp varchar(150) null, 
										ImgeWidthAndHeight_JSON varchar(32) ,
										Type tinyint not null, 
										Mfav   int  not null,
										Commented_count int not null,
										Transferred_count int not null, 
										Time_t datetime not null ,
										primary key (Msg_id,User_id)
										);");
						/*
						,
						constraint fk_t_msg_info_".strtolower($username)."  foreign key(User_id) references users(Uid) 
							 */
							 //end
							/*6. 皮肤skin_somebody表：*/
							//begin
							
								$conditionCheck5 = $pdo->query("create table ".Skin_const.$this->username."(
							Sid int auto_increment primary key not null,
							SimageSrc varchar(150) not null ,
							Scolor varchar(20) not null   
							);");  
							//end

							
							 
						 
							 /*
						4.  用户消息索引表（t_uer_msg_index） ：

						备注：此表就是当我们点击“我的首页”时拉取的消息列表，只是索引，Time_t对这些消息进行排序
							 */
							 // todo --end
							$conditionCheck6 = $pdo->query("create table ".MessageIndex_const.$this->username."(
							User_id varchar(40) not null,
							Author_id int not null,
							Msg_id int not null,
							Time_t datetime not null,
							primary key(User_id,Author_id,Msg_id)
							); 
							");  
							/*
							,
							foreign key (User_id) references users(Uid),
							foreign key (Author_id) references users(Uid)
							*/
							/* 注意这里 Msg_id本身也是外键 但是在创建的时候, 别的用户表 那时候还没有创建 ,因为每个用户注册时候才会建立属于自己的表 所以在没有注册 是不知道表名的  */
							
							echo $conditionCheck2;
							if(!($conditionCheck1&&$conditionCheck2&&$conditionCheck3&&$conditionCheck4&&$conditionCheck5&&$conditionCheck6)){
								throw new PDOException('生成用户表');
							}
								return true;
				}catch(PDOException $exception){
					 

					 die("错误：".$exception->getMessage());
					return false;
				}
		} //end function  
		
	);//end action
	

	//end  medoo  action()
	return $result_creatTable;		
			
		
	}
		
		

	
	
}

?>