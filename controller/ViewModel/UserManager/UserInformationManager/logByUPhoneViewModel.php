<?php
//
//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'ViewModel'.DIRECTORY_SEPARATOR.'viewModelBaseClass.php';

require_once Tool::returnAbsoluteRouteString(['controller','ViewModel','UserManager','UserInformationManager','updateToken.php']);


class GetUserIdByEmailViewModel extends  viewModelBaseClass{

	public function __construct($dbName){    
	    $parent='medoo_returnPDO'; //这里不要使用ViewModel基类 因为那时候还没有userId,token
		
		return $parent::__construct($dbName);
	}
	
	private $UPhone,$userId,$Upasswd,$Ualais,$UimageSrc,$token,$errorArray; 
	function getUserIdByUPhone($pdo,$UPhone,$Upasswd){ //pdo要传入 这个类生成的pdo
	//数组转换为对象,因为action函数需要使用这个,他只有一个实参,所以只能使用变量   

		$this->UPhone= $UPhone; 
		$this->Upasswd = $Upasswd;
		//这里会将 9_11 分割成[9,11]  表明在第9个表的 序号11的位置,注意因为存在事务回滚 ,auto_increment不会减回原样  
		
//这里已经设置了 事务等级 在medoo的PDO构造函数源码 中查找     
//返回值也请查看medoo.php文件 
		$actionReturnValue = $pdo->action(function($pdo){ 
				//检查表是否已经创建了
				try{ //注意没有$号的 都在db.config.php中定义了的常量

					$registerUserTable = Tool::get_table_byTableCount(UserInformation,sha1($this->UPhone),Slice_Table);
					//获得前缀    
					$table_id_prefix = (sha1($this->UPhone)%Slice_Table)+1;
				    $datas=$pdo->select($registerUserTable,[
					'User_id',
					'Ualais',
					'UimageSrc'
					],[
					'and'=>[
						'UPhone'=>$this->UPhone,
						'Upasswd' => $this->Upasswd
						]
					]);//这个在用户注册表中
					if(empty($datas)){
						throw new PDOException('用户或密码错误 来自userInputInformationViewModel');
					}
					$userId__anti_prefix ;
					foreach($datas as $data){
						$userId__anti_prefix = $data['User_id'];
						$this->Ualais =$data['Ualais'];
						$this->UimageSrc = $data['UimageSrc'];
					}
                    //返回前缀和后缀的组合  ,用户id  
					$this->userId = $table_id_prefix.'_'.$userId__anti_prefix;
					//开始提交token 
					if(empty( $this->UimageSrc)){
						$this->UimageSrc ='null';
					}
					$jsonArray = updateToken::getToken($pdo,$this->userId,$this->Ualais,$this->UimageSrc);
					if(!$jsonArray){
						
						Tool::debug_content('来自融云getToken()的错误,'.'请求的用户UPhone:'.$this->UPhone);
						throw new PDOException('融云error');
					}
					
					$this->token =$jsonArray['token'];
					//更新用户的token
					$conditionCheck = updateToken::updateTokenToDatabase($pdo,$this->userId,$this->token);
					if(!$conditionCheck){
						throw new PDOException('数据库error');
					}
					return true;
				}catch(PDOException $exception){
					if($exception->getMessage()=='融云error'){
						$this->errorArray=['code'=>801,'error_msg'=>'来自融云getToken()的错误,请再次登录'];
						return false;
					}else if($exception->getMessage()=='数据库error'){
						$this->errorArray=$this->errorArray=['code'=>801,'error_msg'=>'来自数据库更新的错误,请再次登录'];
						return false;
					}
					//其他错误记录 
					Tool::debug_content( $exception->getMessage(),'根据phone查找用户id --GetUserIdByEmailViewModel() ' );
					return false;
				}
		}); //end action
	//@imoment  这里修改了medoo的源码  错误返回值为 -1,正确为0      
			if($actionReturnValue!= -1){
				return ['code'=>200,'userId'=>$this->userId,'Ualais'=>$this->Ualais,'UimageSrc'=>$this->UimageSrc,'token'=>$this->token];
				//这里返回了用户的表名后缀  
				//这个是submit函数的返回值 
			}else{ //没有写入成功的 事务回滚的  
				if(!empty($this->errorArray)){
					return $this->errorArray;
				}else{
					return '';
				}
				
			}
    }//end class function

}

?>