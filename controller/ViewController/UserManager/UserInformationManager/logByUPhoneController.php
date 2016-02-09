<?php
//这里不写入与数据库有关的代码
//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'viewControllerBaseClass.php'; //class --- Tool


//引入 viewModel ,由controller 传入view 
require_once Tool::returnAbsoluteRouteString( ['controller','ViewModel','UserManager','UserInformationManager','logByUPhoneViewModel.php'
]);
//引入View
require_once Tool::returnAbsoluteRouteString( ['view','UserManager','UserInformationManager','logByUPhoneView.php'
]);



class GetUserIdByEmailController extends viewControllerBaseClass{
    //在构造函数 接受所有GET,POST数据  将他们转换为对象成员 
	/*
	Ualais	 用户注册的昵称                            
	UimageSrc	用户的图片地址  请使用oss返回的地址                  
	UEmailLogin	   用户使用的邮箱地址                          
	Upasswd	    用户的密码建议使用sha1 加密后再上传  不要使用明文                                                           
	Uschool	  选填             
	                             
	*/
	
    public 	$type,$UPhone,$Upasswd;/*userId是那个表名后缀,生成规则请看软件文档,有两种模式,一种根据UEmail更新,另外一种根据userId更新    */
	public function __construct($dbName){ 
	
			if(0 /* Choose_POST_GET */){  //选择使用get还是post
			   if(isset($_POST['type'])){
				   $this->type= $_POST['type'];
			   }
			   if(isset($_POST['UPhone'])){
				   $this->UPhone= $_POST['UPhone'];
			   }
			   
			   if(isset($_POST['Upasswd'])){
				   $this->Upasswd= $_POST['Upasswd'];
			   }

			}else{ 
				
			   if(isset($_GET['type'])){
				   $this->type= $_GET['type'];
			   }
			   if(isset($_GET['UPhone'])){
				   $this->UPhone= $_GET['UPhone'];
			   }
			   if(isset($_GET['Upasswd'])){
				   $this->Upasswd= $_GET['Upasswd'];
			   }
			}
       return parent::__construct($dbName);
	}



}
//初始化controller类 将POST和GET的都变成类成员 <上面if 语句控制接受那种数据>  
$controller1 = new GetUserIdByEmailController(DATABASE_NAME);

//初始化View  
$view =new GetUserIdByEmailView();

//初始化 viewModel 
$viewModel = new GetUserIdByEmailViewModel(DATABASE_NAME);


	if($controller1->UPhone!=''&&$controller1->Upasswd!=''){
		$viewModelResult = $viewModel->getUserIdByUPhone($viewModel,$controller1->UPhone,$controller1->Upasswd);
		if($viewModelResult!=''){  //检查viewModel 访问数据库是否成功  
			echo $view->display_($viewModelResult);

		}else{
			echo $view->display_(['code'=>801,'error_msg'=>'登录失败']); //登录/查询失败   
		}
	}else{
		echo $view->display_(['code'=>800,'error_msg'=>'漏填参数']);//漏填参数  
	}




?>