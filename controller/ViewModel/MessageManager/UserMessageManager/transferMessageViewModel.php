<?php
//
//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'ViewModel'.DIRECTORY_SEPARATOR.'viewModelBaseClass.php';


require_once Tool::returnAbsoluteRouteString(['controller','ViewModel','ViewModelPlug_in','updateMessage_DynamicCount','updateDynamicCount.php']);
/* require_once Tool::returnAbsoluteRouteString(['view','Json_encode_decode.php']); */

class transferMessageViewModel extends  viewModelBaseClass{

	public function __construct($dbName){    
	    //$parent='medoo_returnPDO';   
		
		return parent::__construct($dbName);   
	}

	

	private $Tid,$id,$fromUserId,$toUserId,$messageId,$Ualais; 
//初始化 viewModel 
//注意这里  转发/收藏就是点赞  两个是同等效果  
	function transferMessageByUserId($pdo,$Tid,$id,$fromUserId,$toUserId,$messageId,$Ualais){  //pdo要传入 这个类生成的pdo
	//数组转换为对象,因为action函数需要使用这个,他只有一个实参,所以只能使用变量
		$this->Tid =$Tid ;
		$this->id = $id;
		$this->fromUserId = $fromUserId;
		$this->toUserId = $toUserId;
		$this->messageId = $messageId; 
		$this->Ualais = $Ualais;  
		try{
			if($this->fromUserId==$this->toUserId){ //不能自己点赞自己   
				return ''; //返回空就是代表错误  
			}
			$conditionCheck1=$pdo->select(Favour_const.$this->toUserId,[
			'Userid'
			],[/*toUserId是按下点赞的人 */
			'AND'=>[
				'Userid'=>$this->fromUserId,
				'Messageid'=>$this->messageId
				]
			]);  ////检查是否已经点赞  虽然有唯一性约束  但是这里需要返回提示,所以需要区分数据库操作错误是什么,下面的错误会使用事务进行回滚 所以需要
			$actionReturnValue=-1; //if 有作用域
			if(empty($conditionCheck1)){
				$actionReturnValue = $pdo->action(function($pdo){ 
						//检查表是否已经创建了
						try{ //注意没有$号的 都在db.config.php中定义了的常量

							//如果没有点赞插入,
							//首先检查 fromUserId 不能是自己本身 

							$conditionCheck1= $pdo->insert(Favour_const.$this->toUserId,[
							'Userid'=>$this->fromUserId,
							'Messageid'=>$this->messageId,
							'Ualais'=>$this->Ualais
							]
							);
					
							if(!$conditionCheck1){
								throw new PDOException('fromUserId:'.$this->fromUserId.'  '.$pdo->last_query());

							}
							//点赞后 要更新点赞的数量 ,引入插件类 ---使用static 声明函数,省资源  
						
							$conditionCheck1 =updateDynamicCountPlug_in::updateFavCount($pdo,$this->Tid,$this->id,$this->fromUserId,$this->messageId);
							
							if(!$conditionCheck1){
								throw new PDOException('由插件类记录错误-更新点赞数量-end');
							}
					
						}catch(PDOException $exception){
							Tool::debug_content( $exception->getMessage() );
							return false;
						}
				}); //end action
			}else{
				//已经点赞了的输出
				return ['code'=>201,'error_msg'=>'已经点赞了'];
				
			}
		}catch(PDOException $exception){
			Tool::debug_content( $exception->getMessage() );
			return false;
		}

	//@imoment  这里修改了medoo的源码  错误返回值为 -1,正确为0      
			if($actionReturnValue!= -1){
				return ['code'=>200,'fromUserId'=>$this->fromUserId,'toUserId'=>$this->toUserId];
				//这里返回了用户的表名后缀  
				//这个是submit函数的返回值 
			}else{ //没有写入成功的 事务回滚的  
				return '';
			}

    }//end class function
	

}

?>