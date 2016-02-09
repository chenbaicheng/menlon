<?php
//这里不写入与数据库有关的代码

//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'viewControllerBaseClass.php'; //class --- Tool

//require_once Tool::returnAbsoluteRouteString(['controller','server-sdk-php','ServerAPI.php']);
require_once Tool::returnAbsoluteRouteString(['controller','ViewModel','UserManager','UserInformationManager','updateToken.php']);

require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'databaseAccessProxy_Model'.DIRECTORY_SEPARATOR.'medoo_returnPDO.php'; //class Tool


require_once Tool::returnAbsoluteRouteString(['view','Json_encode_decode.php']);

require_once Tool::returnAbsoluteRouteString(['view','UserManager','UserInformationManager','tokenView.php']);
class TokenController extends viewControllerBaseClass{
    //在构造函数 接受所有GET,POST数据  将他们转换为对象成员 
	/*                            
	*/
	
    public 	$userId,$Ualais,$imgurl;/*userId是那个表名后缀,生成规则请看软件文档,有两种模式,一种根据UEmail更新,另外一种根据userId更新    */
	public function __construct($dbName=''){ 
	
			if(Choose_POST_GET){  //选择使用get还是post
			   if(isset($_POST['userId'])){
				   $this->userId= $_POST['userId'];
			   }
			   if(isset($_POST['Ualais'])){
				   $this->Ualais= $_POST['Ualais'];
			   }
			   
			   if(isset($_POST['imgurl'])){
				   $this->imgurl= $_POST['imgurl'];
			   }

			}else{ 
				
			   if(isset($_GET['userId'])){
				   $this->userId= $_GET['userId'];
			   }
			   if(isset($_GET['Ualais'])){
				   $this->Ualais= $_GET['Ualais'];
			   }
			   
			   if(isset($_GET['imgurl'])){
				   $this->imgurl= $_GET['imgurl'];
			   }
			}
       return parent::__construct($dbName);
	}
	
	function getToken(&$pdo){
		//获取token 
			$jsonArray = updateToken::getToken($pdo,$this->userId,$this->Ualais,$this->imgurl);
			if($jsonArray ===false){
				return '';
			}
			//更新表格
			$conditionCheck2 = updateToken::updateTokenToDatabase($pdo,$this->userId,$jsonArray['token']);
			if($conditionCheck2===false){
				return '';
			}
			return $jsonArray;
			/* //检查userid是否存在,使用  $this->checkUserIdIsCorrect();
			$checkResult = $this->checkUserIdIsCorrect($this->userId);
			if(!$checkResult){//数据库错误检查 
				//不存在 
				echo $tokenView->display_(['code'=>801,'error_msg'=>'参数错误']);
				return '';
			}
			$p = new ServerAPI(RONG_YUN_APPKEY,RONG_YUN_APPSECRET);
			$r = $p->getToken($this->userId,$this->Ualais,$this->imgurl);
			$arrayFromJson = JSON_Array_Control::JsonString_to_array($r);
			if($arrayFromJson['code']==200){ //这个是融云的错误提示
				echo $r; //这个$r 本身就是json 所以不用转换
			}else{
				//对于200以外  全部返回错误提示  这里就不借用view层直接输出 
				echo $tokenView->display_(['code'=>801,'error_msg'=>'参数错误']);
			} */

	}
	
	function userRefresh(){
			$p = new ServerAPI(RONG_YUN_APPKEY,RONG_YUN_APPSECRET);
			$r = $p->userRefresh($this->userId,$this->Ualais,$this->imgurl);
			return $r;
	}
	//检查userid是否存在 ,如果不存在 就返回false, 不允许获取token  
	

}
//初始化controller类 将POST和GET的都变成类成员 <上面if 语句控制接受那种数据>  

$TokenObj =new TokenController(DATABASE_NAME); //这里需要沟通数据库,验证userId是否有效  
$tokenView =new tokenView();
$pdo = new medoo_returnPDO(DATABASE_NAME);
$returnValue = $TokenObj->getToken($pdo);  //这里会直接输出到view层 不用自己手动输出 
if($returnValue !== false){
	echo $tokenView->display_($returnValue);
}else{
	echo $tokenView->display_(['code'=>801,'error_msg'=>'参数错误']);

}
?>