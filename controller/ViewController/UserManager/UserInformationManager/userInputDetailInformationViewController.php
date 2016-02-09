<?php
//这里不写入与数据库有关的代码
//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'viewControllerBaseClass.php'; //class Tool


//引入 viewModel ,由controller 传入view 
require_once Tool::returnAbsoluteRouteString( ['controller','ViewModel','UserManager','UserInformationManager','userInputDetailInformationViewModel.php'
]);
//引入View
require_once Tool::returnAbsoluteRouteString( ['view','UserManager','UserInformationManager','userInputDetailInformationView.php'
]);




class userInputDetailInformationController extends viewControllerBaseClass{
    //在构造函数 接受所有GET,POST数据  将他们转换为对象成员 
	/*
	Ualais	 用户注册的昵称                            
	UimageSrc	用户的图片地址  请使用oss返回的地址                  
	UEmailLogin	   用户使用的邮箱地址                          
	Upasswd	    用户的密码建议使用sha1 加密后再上传  不要使用明文                                                           
	Uschool	  选填             
	                             
	*/
	
    public 	$type,$Usex,$Uschool,$UschoolIndex,$Uskin,$Uinfo,$userEmail,$userId,$Ualais,$UimageSrc,$Utel;/*userId是那个表名后缀,生成规则请看软件文档,有两种模式,一种根据UEmail更新,另外一种根据userId更新    */
	public function __construct($dbName){ 
	/* 	这个是错误的	$this->Usex =$InformationArray['Usex'];
		$this->Uschool =$InformationArray['Uschool'];
		$this->Uskin = $InformationArray['Uskin'];
		$this->Utel = $InformationArray['Utel'];
		$this->Uinfo=$InformationArray['Uinfo']; */
		
			if(Choose_POST_GET){  //选择使用get还是post
			   if(isset($_POST['type'])){
				   $this->type= $_POST['type'];
			   }
			   if(isset($_POST['Ualais'])){
				   $this->Ualais=$_POST['Ualais'];
			   }
			   if(isset($_POST['Utel'])){
				   $this->Utel = $_POST['Utel'];
			   }
			   if(isset($_POST['UimageSrc'])){
				   $this->UimageSrc=$_POST['UimageSrc'];
			   }
			   if(isset($_POST['Usex'])){
				   $this->Usex= $_POST['Usex'];
			   }
			   if(isset($_POST['UschoolIndex'])){
				   $this->UschoolIndex= $_POST['UschoolIndex'];
			   }
			   if(isset($_POST['Uschool'])){
				   $this->Uschool= $_POST['Uschool'];
			   }
			   if(isset($_POST['Uskin'])){
				   $this->Uskin= $_POST['Uskin'];
			   }
			   if(isset($_POST['Utel'])){
				   $this->Utel= $_POST['Utel'];
			   }
			   if(isset($_POST['Uinfo'])){
				   $this->Uinfo= $_POST['Uinfo'];
			   }
			   if(isset($_POST['userEmail'])){
				   $this->userEmail= $_POST['userEmail'];
			   }
			   if(isset($_POST['userId'])){
				   $this->userId= $_POST['userId'];
			   }
			}else{ 
				
			   if(isset($_GET['type'])){
				   $this->type= $_GET['type'];
			   }
			   
			   if(isset($_GET['Ualais'])){
				   $this->Ualais=$_GET['Ualais'];
			   }
			   if(isset($_GET['Utel'])){
				   $this->_GET = $_GET['Utel'];
			   }
			   if(isset($_GET['UimageSrc'])){
				   $this->UimageSrc=$_GET['UimageSrc'];
			   }
			   
			   if(isset($_GET['Usex'])){
				   $this->Usex= $_GET['Usex'];
			   }
			   if(isset($_GET['UschoolIndex'])){
				   $this->UschoolIndex= $_GET['UschoolIndex'];
			   }
			   if(isset($_GET['Uschool'])){
				   $this->Uschool= $_GET['Uschool'];
			   }
			   if(isset($_GET['Uskin'])){
				   $this->Uskin= $_GET['Uskin'];
			   }
			   if(isset($_GET['Utel'])){
				   $this->Utel= $_GET['Utel'];
			   }
			   if(isset($_GET['Uinfo'])){
				   $this->Uinfo= $_GET['Uinfo'];
			   }
			   if(isset($_GET['userEmail'])){
				   $this->userEmail= $_GET['userEmail'];
			   }
			   if(isset($_GET['userId'])){
				   $this->userId= $_GET['userId'];
			   }
			}
       return parent::__construct($dbName);
	}



}
//初始化controller类 将POST和GET的都变成类成员 <上面if 语句控制接受那种数据>  
$controller1 = new userInputDetailInformationController(DATABASE_NAME);
//获取步骤类型  
$type_global =$controller1->type;

//初始化viewModel
$viewModel = new userInputDetailInformationViewModel(DATABASE_NAME);

//初始化view 
$view = new userInputDetailInformationView();

if($type_global =='userId'){
	//检查Uschool 和UschoolIndex是否有填写
	$boolValue=true;
	if($new1->Uschool!=''||$new1->UschoolIndex!=''){
		if($new1->Uschool==''||$new1->UschoolIndex==''){  //这里说明填写了其中一个 但是另外一个为空  ,所以参数出错
			$boolValue=false;
			
		}
	}
	if($controller1->userId!=''&&$controller1->Usex!=''&&$controller1->userEmail!=''&&$controller1->Uinfo!=''&&($boolValue) ){
		$viewModel_return = $viewModel->InputInformationByuserId(
		$viewModel,
		$controller1->userId
		,[
		'Usex'=>$controller1->Usex,
		'Uskin'=>$controller1->Uskin,
		'UschoolIndex'=>$controller1->UschoolIndex,
		'Uschool'=>$controller1->Uschool,
		'UEmail'=>$controller1->userEmail,
		'Utel'=>$controller1->Utel,
		'UimageSrc'=>$controller1->UimageSrc,
		'Uinfo'=>$controller1->Uinfo
		]);
		if($viewModel_return!=''){
			echo $view->display_($viewModel_return);//输出成功信息
		}else{
			echo $view->display_(['code'=>801,'error_msg'=>'输入参数不合法']);//输出失败信息
		}
	}else{
		echo $view->display_(['code'=>800,'error_msg'=>'参数漏填']); //输出参数漏填信息
	}
}
/* if($type_global == 'email'){
	if($controller1->userEmail!=''&&$controller1->Usex!=''&&$controller1->Utel!=''&&$controller1->Uinfo!=''){
		$returnValue = $viewModel->InputInformationByUserEmail(
		$viewModel,
		$controller1->userEmail
		,[
		'Usex'=>$controller1->Usex,
		'Uschool'=>$controller1->Uschool,
		'Uskin'=>$controller1->Uskin,
		'Utel'=>$controller1->Utel,
		'Uinfo'=>$controller1->Uinfo
		]);
		if($viewModel_return!=''){
			echo $view->display_($viewModel_return);//输出成功信息
		}else{
			echo $view->display_(['code'=>1]);//输出失败信息
		}
	}else{
		echo $view->display_(['code'=>800]); //输出参数漏填信息
	}
} */

if($type_global==''){
		echo $view->display_(['code'=>800]); //输出参数漏填信息
}

?>