<?php
//这里不写入与数据库有关的代码
//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'viewControllerBaseClass.php'; //class Tool


//引入 viewModel ,由controller 传入view 
require_once Tool::returnAbsoluteRouteString( ['controller','ViewModel','HomePage','userInformationTitleViewModel.php'
]);
//引入View
require_once Tool::returnAbsoluteRouteString( ['view','HomePage','userInformationTitleView.php'
]);

class userInformationTitleViewController extends viewControllerBaseClass{
    //在构造函数 接受所有GET,POST数据  将他们转换为对象成员 

    public 	$type,$userId;
	public function __construct($dbName){ 
			if(Choose_POST_GET){  //选择使用get还是post
				if(isset($_POST['userId'])){
				   $this->userId=$_POST['userId'];
				}
				if(isset($_POST['type'])){
				   $this->type=$_POST['type'];
				}
			}else{ 
				if(isset($_GET['userId'])){
				   $this->userId=$_GET['userId'];
				}
				if(isset($_GET['type'])){
				   $this->type=$_GET['type'];
				}
			
			}
       return parent::__construct($dbName);
	}



}
//初始化controller类 将POST和GET的都变成类成员 <上面if 语句控制接受那种数据>  
$controllerObj = new userInformationTitleViewController(DATABASE_NAME);
//获取步骤类型  
$type_global =$controllerObj->type; 
//初始化viewModel
$viewModel = new userInformationTitleViewModel(DATABASE_NAME);


//初始化view 
$view = new userInformationTitleView();
if($type_global=='getUserTitle'){
	$result=$viewModel->getUserTitle($viewModel,$controllerObj->userId);//function getUserTitle($pdo,$userId);
	if($result!=''){
		echo $view->display_($result);
	}else{
		echo $view->display_(['code'=>'801','error_msg'=>'请稍后再试']);
	}
}else{
	echo $view->display_(['code'=>'800','error_msg'=>'参数漏填']);
}
    

?>