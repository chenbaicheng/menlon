<?php
//这里不写入与数据库有关的代码
//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'viewControllerBaseClass.php'; //class --- Tool


//引入 viewModel ,由controller 传入view 
require_once Tool::returnAbsoluteRouteString( ['controller','ViewModel','Find','FindManagerModule','FindManagerViewModule.php'
]);
//引入View
require_once Tool::returnAbsoluteRouteString( ['view','Find','FindManagerModule','FindManagerModuleView.php'
]);



class FindManagerViewController extends viewControllerBaseClass{
    //在构造函数 接受所有GET,POST数据  将他们转换为对象成员 

	public $page,$type,$titleName,$schoolName,$Ualais;
	public function __construct($dbName){ 
	
			if(Choose_POST_GET){  //选择使用get还是post
			//评论
				if(isset($_POST['type'])){
				   $this->type= $_POST['type'];
				}
				if(isset($_POST['page'])){
				   $this->page= $_POST['page'];
				}
				if(isset($_POST['titleName'])){
				   $this->titleName= $_POST['titleName'];
				}
				if(isset($_POST['schoolName'])){
				   $this->schoolName= $_POST['schoolName'];
				}
				if(isset($_POST['Ualais'])){
				   $this->Ualais= $_POST['Ualais'];
				}
			}else{
				if(isset($_GET['type'])){
				   $this->type= $_GET['type'];
				}
				if(isset($_GET['page'])){
				   $this->page= $_GET['page'];
				}
				if(isset($_GET['titleName'])){
				   $this->titleName= $_GET['titleName'];
				}
				if(isset($_GET['schoolName'])){
				   $this->schoolName= $_GET['schoolName'];
				}
				if(isset($_GET['Ualais'])){
				   $this->Ualais= $_GET['Ualais'];
				}
			}
       return parent::__construct($dbName);
	}



}
//初始化controller类 将POST和GET的都变成类成员 <上面if 语句控制接受那种数据>  
$controller1 = new FindManagerViewController('');

//初始化View  
$view =new FindManagerModuleView();

//初始化 viewModel 
$viewModel = new FindManagerViewModule(DATABASE_NAME); //这是用户注册表的数据库  

//话题搜索  
if($controller1->type=='findTitleByTitleName'){
	if($controller1->page!=''&&$controller1->titleName!=''){  //这里不用检查type 在前面的if已经检查了  ,这里是原创信息发布  
	//将 高宽 打包成数组   

	//**************
		$viewModelResult = FindManagerViewModule::findTitleByTitleName($viewModel,$controller1->titleName,$controller1->page
		);
		if($viewModelResult!=''){  //检查viewModel 访问数据库是否成功  
			echo $view->display_($viewModelResult);  //输出到视图  

		}else{
			echo $view->display_(['code'=>801,'error_msg'=>'查询失败,请稍后再试']); //登录/查询失败   
		}
	}else{
		echo $view->display_(['code'=>800,'error_msg'=>'漏填参数']);//漏填参数  
	}
}

//学校搜索  
if($controller1->type=='findSchoolBySchoolName'){ 
	if($controller1->schoolName!=''){  //这里不用检查type 在前面的if已经检查了  ,这里是原创信息发布  
	//将 高宽 打包成数组   

	//**************
		$viewModelResult = FindManagerViewModule::findSchoolBySchoolName($viewModel,$controller1->schoolName);
		if($viewModelResult!=''){  //检查viewModel 访问数据库是否成功  
			echo $view->display_($viewModelResult);  //输出到视图  

		}else{
			echo $view->display_(['code'=>801,'error_msg'=>'查询失败,请稍后再试']); //登录/查询失败   
		}
	}else{
		echo $view->display_(['code'=>800,'error_msg'=>'漏填参数']);//漏填参数  
	}
}

//用户查找
if($controller1->type=='findUserByUalais'){ 
	if($controller1->Ualais!=''&&$controller1->page!=''){  //这里不用检查type 在前面的if已经检查了  ,这里是原创信息发布  
	//将 高宽 打包成数组   

	//**************
		$viewModelResult = FindManagerViewModule::findUserByUalais($viewModel,$controller1->Ualais,$controller1->page);
		if($viewModelResult!=''){  //检查viewModel 访问数据库是否成功  
			echo $view->display_($viewModelResult);  //输出到视图  

		}else{
			echo $view->display_(['code'=>801,'error_msg'=>'查询失败,请稍后再试']); //登录/查询失败   
		}
	}else{
		echo $view->display_(['code'=>800,'error_msg'=>'漏填参数']);//漏填参数  
	}
}
?>