<?php
//
//***** 
//***** 
//*****
//***************  \controller\ViewModel
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'ViewModel'.DIRECTORY_SEPARATOR.'viewModelBaseClass.php'; //class Tool



class userInputDetailInformationViewModel extends  viewModelBaseClass{

	public function __construct($dbName){    
	   // $parent='viewModelBaseClass';
		
		return parent::__construct($dbName);
	}

	
	public $pdo,$userId,$useToSeachTable_KeyWordArray,$updateArray;
	function InputInformationByuserId($pdo,$userId,$updateArray){ //pdo要传入 这个类生成的pdo
	//数组转换为对象,因为action函数需要使用这个,他只有一个实参,所以只能使用变量   
		
		$this->userId =$userId;
		$this->updateArray = $updateArray;

		$this->useToSeachTable_KeyWordArray =Tool::slice_userId_ReturnArray($this->userId); //这里会将 9_11 分割成[9,11]  表明在第9个表的 序号11的位置,注意因为存在事务回滚 ,auto_increment不会减回原样  
		
//这里已经设置了 事务等级 在medoo的PDO构造函数源码 中查找 
//返回值也请查看medoo.php文件 
		$actionReturnValue = $pdo->action(function($pdo){ 
				//检查表是否已经创建了
				try{ //注意没有$号的 都在db.config.php中定义了的常量  
					$conditionCheck1 = $pdo->update(UserInformation.$this->useToSeachTable_KeyWordArray[0],$this->updateArray,[
					'User_id'=>$this->useToSeachTable_KeyWordArray[1]
					]);
/* 					$pdo->debugOutput($pdo->last_query());
					$pdo->debugOutput($this->useToSeachTable_KeyWordArray[1],'注册表用户行数'); */
					if(!$conditionCheck1){
						throw new PDOException('更新注册用户数据失败 来自userRegisterViewModel');
					}
					
				}catch(PDOException $exception){
					
					Tool::debug_content( $exception->getMessage() );
					return false;
				}
		}); //end action  transaction 
	//@imoment  这里修改了medoo的源码  错误返回值为 -1,正确为0
			if($actionReturnValue!= -1){
				return ['code'=>200,'userId'=>$this->userId];
				//这里返回了用户的表名后缀  
				
			}else{ //没有写入成功的 事务回滚的  
				return '';
			}
    }//end class function
	
	//根据邮箱更新用户 ,首先要计算出用户在那个表格  ,然后在那个表格找到用户所在的行数 ,组合成表名后缀, 根据后缀 更新表格  
	//*******因为这个方法是类通用的  所以在基类 类中实现 **************
	
/* 	public $UEmail; 
	function InputInformationByUserEmail($pdo,$,$UEmail,$updateArray){ //pdo要传入 这个类生成的pdo
	//数组转换为对象,因为action函数需要使用这个,他只有一个实参,所以只能使用变量   
		$this->updateArray=$updateArray;
		$this->UEmail= $UEmail; 
		//这里会将 9_11 分割成[9,11]  表明在第9个表的 序号11的位置,注意因为存在事务回滚 ,auto_increment不会减回原样  
		
//这里已经设置了 事务等级 在medoo的PDO构造函数源码 中查找     
//返回值也请查看medoo.php文件 
		$actionReturnValue = $pdo->action(function($pdo){ 
				//检查表是否已经创建了
				try{ //注意没有$号的 都在db.config.php中定义了的常量  
					$table_id=sha1($UEmail)%Slice_Table;
				    $datas=$pdo->select(UserInformation.$table_id,[
					'User_id'
					],[
					'UEmail'=>$this->UEmail
					]);//这个在用户注册表中
					if(!$datas){
						throw new PDOException('获取注册用户id失败 来自userInputInformationViewModel');
					}
					$userId__anti_prefix ;
					foreach($datas as $data){
						$userId__anti_prefix = $data['User_id'];
					}
					$conditionCheck1 = $pdo->update(UserInformation.$table_id,$updateArray,[
					'User_id'=>$userId__anti_prefix
					]);
					if(!$conditionCheck1){
						throw new PDOException('更新注册用户数据失败 来自userInputInformationViewModel');
					}
					
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
    }//end class function */
	
	
} //end class

?>