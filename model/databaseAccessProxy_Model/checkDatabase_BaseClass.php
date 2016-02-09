<?php 
/* 基类已经包含了下面文件
require_once('medoo.php');
require_once('db.config');
直接导入基类
*/
require_once('medoo_returnPDO.php');
class checkDatabase_BaseClass extends medoo_returnPDO{

	public $db;
	public function check_database_isExit(){	
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
		try{  
			$this->db=new PDO("mysql:host=".DATABASE_SERVER.";port=".DATABASE_PORT.";dbname=".DATABASE_NAME,DATABASE_USER,DATABASE_PWD);
			//echo "success";
		} catch (PDOException $e){
			return '{"error":500 from checkDatabase_BaseClass}';
		}						
			
			
		$rs =$this->db->exec("use database ".$this->db_name);
		if($rs!==true/*某个数据库已经存在  $rs!==true  */){	 
			return 1;     //存在
		}else{
			  //创建失败,请检查用户名是否有重复或者正确  
		   return 0;
			
		}
		return 0;
	}
	public function check_dataTableIsExist(){ //检查数据库表是否存在  
		
	}
	
}
?>