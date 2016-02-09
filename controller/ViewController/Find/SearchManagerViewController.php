<?php
//这里不写入与数据库有关的代码
//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'viewControllerBaseClass.php'; //class --- Tool


//引入 viewModel ,由controller 传入view 
require_once Tool::returnAbsoluteRouteString( ['controller','ViewModel','Find','SearchManagerViewModel.php'
]);
//引入View
require_once Tool::returnAbsoluteRouteString( ['view','Find','SearchManagerView.php'
]);



class SearchManagerViewController extends viewControllerBaseClass{
    //在构造函数 接受所有GET,POST数据  将他们转换为对象成员 
	/*
	Ualais	 用户注册的昵称                            
	UimageSrc	用户的图片地址  请使用oss返回的地址                  
	UEmailLogin	   用户使用的邮箱地址                          
	Upasswd	    用户的密码建议使用sha1 加密后再上传  不要使用明文                                                           
	Uschool	  选填             
	                             
	*/

	public $page,$getNumber,$type,$Tid,$schoolId;
	public function __construct($dbName){ 
	
			if(Choose_POST_GET){  //选择使用get还是post
			//评论
				if(isset($_POST['type'])){
				   $this->type= $_POST['type'];
				}
				if(isset($_POST['page'])){
				   $this->page= $_POST['page'];
				}
				if(isset($_POST['Tid'])){
				   $this->Tid= $_POST['Tid'];
				}
				if(isset($_POST['schoolId'])){
				   $this->schoolId= $_POST['schoolId'];
				}
				if(isset($_POST['getNumber'])){
				   $this->getNumber= $_POST['getNumber'];
				}
			}else{
				if(isset($_GET['type'])){
				   $this->type= $_GET['type'];
				}
				if(isset($_GET['page'])){
				   $this->page= $_GET['page'];
				}
				if(isset($_GET['Tid'])){
				   $this->Tid= $_GET['Tid'];
				}
				if(isset($_GET['schoolId'])){
				   $this->schoolId= $_GET['schoolId'];
				}
				if(isset($_GET['getNumber'])){
				   $this->getNumber= $_GET['getNumber'];
				}
			}
       return parent::__construct($dbName);
	}



}
//初始化controller类 将POST和GET的都变成类成员 <上面if 语句控制接受那种数据>  
$controller1 = new SearchManagerViewController('');

//初始化View  
$view =new SearchManagerView();

//初始化 viewModel 
$viewModel = new SearchManagerViewModel(DATABASE_NAME); //这是用户注册表的数据库  

if($controller1->type=='searchByHotValue'){//热度搜索 
	if($controller1->page!=''){  //这里不用检查type 在前面的if已经检查了  ,这里是原创信息发布  
	//将 高宽 打包成数组   

	//**************
		$viewModelResult = SearchManagerViewModel::searchByHotValue($viewModel,$controller1->page
		);
		if($viewModelResult!=''){  //检查viewModel 访问数据库是否成功  
			echo $view->display_($viewModelResult);  //输出到视图  

		}else{
			echo $view->display_(['code'=>801,'error_msg'=>'发布失败,请稍后再试']); //登录/查询失败   
		}
	}else{
		echo $view->display_(['code'=>800]);//漏填参数  
	}
}
if($controller1->type=='searchByHotMessageTitle'){ //话题搜索 
	//**************
	
	if($controller1->page==''&&$controller1->getNumber==''){
		$viewModelResult = SearchManagerViewModel::searchByHotMessageTitle($viewModel);
	}else if($controller1->page!=''&&$controller1->getNumber!=''){
		$viewModelResult = SearchManagerViewModel::searchByHotMessageTitle($viewModel,$controller1->page,$controller1->getNumber);
	}else{
		$viewModelResult=['code'=>800,"error_msg"=>'page和getNumber不能只赋值其中一个'];
	}
	
	if($viewModelResult!=''){  //检查viewModel 访问数据库是否成功  
		echo $view->display_($viewModelResult);  //输出到视图  

	}else{
		echo $view->display_(['code'=>801,'error_msg'=>'查找失败,请稍后再试']); //登录/查询失败   
	}
}

if($controller1->type=='getMessageByTid'){ //根据话题Tid 获取此话题下所有帖子 
	//**************
	if($controller1->page!=''&&$controller1->Tid!=''){ //function getMessageByTid(&$pdo,$Tid,$page);
		$viewModelResult = SearchManagerViewModel::getMessageByTid($viewModel,$controller1->Tid,$controller1->page);
		if($viewModelResult!=''){  //检查viewModel 访问数据库是否成功  
			echo $view->display_($viewModelResult);  //输出到视图  

		}else{
			echo $view->display_(['code'=>801,'error_msg'=>'查找失败,请稍后再试']); //登录/查询失败   
		}
	}else{
		echo $view->display_(['code'=>800,'error_msg'=>'漏填参数']);//漏填参数  
	}

}

if($controller1->type=='searchByHotSchoolTitle'){ //学校排名搜索 
	//*****根据$page ,$getNumber 重载不同的函数*********
	if($controller1->page==''&&$controller1->getNumber==''){
		//static function searchByHotSchoolTitle(&$pdo,$page=1);
		$viewModelResult = SearchManagerViewModel::searchByHotSchoolTitle($viewModel);
	}else if($controller1->page!=''&&$controller1->getNumber!=''){
		$viewModelResult = SearchManagerViewModel::searchByHotSchoolTitle($viewModel,$controller1->page,$controller1->getNumber);
	}else{
		$viewModelResult=['code'=>800,"error_msg"=>'page和getNumber不能只赋值其中一个'];
	}

	if($viewModelResult!=''){  //检查viewModel 访问数据库是否成功  
		echo $view->display_($viewModelResult);  //输出到视图  

	}else{
		echo $view->display_(['code'=>801,'error_msg'=>'查找失败,请稍后再试']); //登录/查询失败   
	}
}

if($controller1->type=='getMessageBySchoolId'){ //根据话题Tid 获取此话题下所有帖子 
	//**************
	if($controller1->page!=''&&$controller1->schoolId!=''){ //function getMessageByTid(&$pdo,$Tid,$page);
		$viewModelResult = SearchManagerViewModel::getMessageBySchoolId($viewModel,$controller1->schoolId,$controller1->page);
		if($viewModelResult!=''){  //检查viewModel 访问数据库是否成功  
			echo $view->display_($viewModelResult);  //输出到视图  

		}else{
			echo $view->display_(['code'=>801,'error_msg'=>'查找失败,请稍后再试']); //登录/查询失败   
		}
	}else{
		echo $view->display_(['code'=>800,'error_msg'=>'漏填参数']);//漏填参数  
	}

}

?>