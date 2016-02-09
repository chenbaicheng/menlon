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
class object_databaseCreateProxy_model  extends  checkDatabase_BaseClass{

	public function __construct(){     .


	}
	public function check_database_isExit(){
		//创建的时候已经检查了  所以这里不用检查   
	}
	public  function check_user_table_isexit_then_create(){ //因为是根据用户名表分割表的 
				//check username isempty?
			if($username==''){
				return {'error':'500'}; //用户名没有传入
				
			}else {
				//check table isCreate?
					if(/*某个数据库已经存在*/){
						/*
try{  
        $this->db=new PDO("$this->type:host=$this->db_host;dbname=cc",$this->username,$this->password);
        echo "success";
     } catch (PDOException $e){
        echo "can not connect to this database";
     }						
						
						
$rs =$this->db->exec("use database ".$this->db_name);
if($rs!==true) echo 'connected';
else echo 'can not open database';

						*/
						
						return {'error':'501'};  //创建失败,请检查用户名是否有重复或者正确
					}else if(/*某个数据表已经存在*/){
						return {'error':'501'};  //表名创建失败,请检查用户名是否有重复或者正确

					}else{
						//create pdo 
							create_database($username);
							create_initial_objectTable($username);
						//destroy pdo 
						
							return 0;
						
						
					}
			   return {'error':'error come from initial_object_databaseAccessProxy_model '}; //未知原因 
			}
		return {'error':'error come from initial_object_databaseAccessProxy_model '}; //未知原因 
	}

	
	
}

?>