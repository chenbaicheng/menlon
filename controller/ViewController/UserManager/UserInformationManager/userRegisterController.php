<?php
//这里不写入与数据库有关的代码
//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'viewControllerBaseClass.php'; //class Tool


//引入 viewModel ,由controller 传入view 
require_once Tool::returnAbsoluteRouteString( ['controller','ViewModel','UserManager','UserInformationManager','userRegisterViewModelFromDatabasebackup.php' 
]); //这里先使用备份文件,因为源文件添加了线程  
//引入View
require_once Tool::returnAbsoluteRouteString( ['view','UserManager','UserInformationManager','userRegisterView.php'
]);
// 引入发送短信的类
require_once Tool::returnAbsoluteRouteString(['controller','ViewModel','UserManager','UserInformationManager','sentSMS.php']);



class userRegisterController extends viewControllerBaseClass{
    //在构造函数 接受所有GET,POST数据  将他们转换为对象成员 
	/*
	Ualais	 用户注册的昵称                            
	UimageSrc	用户的图片地址  请使用oss返回的地址                  
	UEmailLogin	   用户使用的邮箱地址                          
	Upasswd	    用户的密码建议使用sha1 加密后再上传  不要使用明文                                                           
	Uschool	  选填             
	                             
	*/
    public 	$Ualais,$UimageSrc,$UPhone,$Upasswd,$UschoolIndex,$Uschool,$type,$Uid,$checkNum;
	public function __construct($dbName){ 
			if(Choose_POST_GET){  //选择使用get还是post
			   if(isset($_POST['checkNum'])){
				   $this->checkNum=$_POST['checkNum'];
			   }
			   if(isset($_POST['Ualais'])){
				   $this->Ualais= $_POST['Ualais'];
			   }
			   if(isset($_POST['UimageSrc'])){
				   $this->UimageSrc= $_POST['UimageSrc'];
			   }
			   if(isset($_POST['UPhone'])){
				   $this->UPhone= $_POST['UPhone'];
			   }
			   if(isset($_POST['Upasswd'])){
				   $this->Upasswd= $_POST['Upasswd'];
			   }
			   if(isset($_POST['UschoolIndex'])){
				   $this->UschoolIndex=$_POST['UschoolIndex'];
			   }
			   if(isset($_POST['Uschool'])){
				   $this->Uschool= $_POST['Uschool'];
			   }
			   if(isset($_POST['type'])){
				   $this->type= $_POST['type'];
			   }
			   if(isset($_POST['Uid'])){
				   $this->type= $_POST['Uid'];
			   }
			}else{ 
			   if(isset($_GET['checkNum'])){
				   $this->checkNum=$_GET['checkNum'];
			   }
			   if(isset($_GET['Ualais'])){
				   $this->Ualais= $_GET['Ualais'];
			   }

			   if(isset($_GET['UimageSrc'])){
				   $this->UimageSrc= $_GET['UimageSrc'];
			   }

			   if(isset($_GET['UPhone']) ){
				   $this->UPhone= $_GET['UPhone'];
			   }
			   
			  
			   if(isset($_GET['Upasswd']) ){
				   $this->Upasswd= $_GET['Upasswd'];
			   }
			   if(isset($_GET['UschoolIndex'])){
				   $this->UschoolIndex=$_GET['UschoolIndex'];
			   }
			   if(isset($_GET['Uschool'] ) ){
				   $this->Uschool= $_GET['Uschool'];
			   }
			   

			   if(isset($_GET['type']) ){
				   $this->type= $_GET['type'];
			   }
			   if(isset( $_GET['Uid'] )){
				   $this->Uid=$_GET['Uid'];
			   }
			
			}
       return parent::__construct($dbName);
	}


	public static function getTableName($obj){
		$uid=sha1($obj->UPhone);
		
		$table_name = Tool::get_table_byTableCount(UserInformation,$uid,Slice_Table); //就是会分10张表 ,根据常量的大小

		return $table_name;
	}

}
//初始化controller类 将POST和GET的都变成类成员 <上面if 语句控制接受那种数据>  
$new1 = new userRegisterController(DATABASE_NAME);
//获取步骤类型  
$type_global =$new1->type;
//初始化viewModel
$viewModel = new userRegisterViewModelFromDatabase(DATABASE_NAME);


//初始化view 
$view = new userRegisterView();


if($type_global == 'sentSMS'){  //这个是获取手机验证码的   
//	function phoneNumberCheck($pdo,$UPhone) ;
	if($new1->UPhone!=''){
		$phoneCheckobj = new SentSMSForlogin();
		$arrayResult = $phoneCheckobj->phoneNumberCheck($viewModel,$new1->UPhone);
		if($arrayResult!=''){
			echo $view->display_($arrayResult);
		}else{
			echo $view->display_(['code'=>801,'error_msg'=>'数据库访问失败']); //输出参数漏填信息

		}

	}else{
		echo $view->display_(['code'=>800,'error_msg'=>'漏填参数']); //输出参数漏填信息
	}
}

if( $type_global =='submit'){
	//检查Uschool 和UschoolIndex是否有填写
	$boolValue=true;
	if($new1->Uschool!=''||$new1->UschoolIndex!=''){
		if($new1->Uschool==''||$new1->UschoolIndex==''){  //这里说明填写了其中一个 但是另外一个为空  ,所以参数出错
			$boolValue=false;
			
		}
	}
	if($new1->Ualais!=''&&$new1->Upasswd!=''&&$new1->UPhone!=''&&$new1->checkNum!=''&&($boolValue/*看上面*/) ){
   //参数检查 成功后执行  
		$hashValue=sha1($new1->UPhone);
		$submitArray=[
		'uuid'=>$hashValue,
		'Ualais'=>$new1->Ualais,
		'UschoolIndex'=>$new1->UschoolIndex,
		'Uschool'=>$new1->Uschool,
		'Upasswd'=>$new1->Upasswd,
		'UimageSrc'=>$new1->UimageSrc,
		'UPhone'=>$new1->UPhone,
		'Udatetime'=>TimeForChina
		];
		//echo date('Y-m-d H:i:s',time());
		$viewModel_return = $viewModel->register(
		$new1->getTableName($new1),
		$new1->UPhone,
		$new1->checkNum,
		$viewModel,$submitArray);
		//echo date('Y-m-d H:i:s',time());
		//var_dump( $viewModel_return);
		//@imoment  这里修改了medoo的源码  错误返回值为 '',正确为0
		if($viewModel_return!=''){
			echo $view->display_($viewModel_return);//输出成功信息
		}else{
			echo $view->display_(['code'=>801,'error_msg'=>'注册失败,请稍后再试']);//输出失败信息
		}
   }else{
		echo $view->display_(['code'=>800,'error_msg'=>'漏填参数']); //输出参数漏填信息
 
   }
    
   
}

?>