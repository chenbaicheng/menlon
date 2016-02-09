<?php
//这里不写入与数据库有关的代码
//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'viewControllerBaseClass.php'; //class --- Tool


//引入 viewModel ,由controller 传入view 
require_once Tool::returnAbsoluteRouteString( ['controller','ViewModel','MessageManager','UserMessageManager','getMessageViewModel.php'
]);
//引入View
require_once Tool::returnAbsoluteRouteString( ['view','MessageManager','UserMessageManager','getMessageView.php'
]);



class getMessageViewController extends viewControllerBaseClass{
    //在构造函数 接受所有GET,POST数据  将他们转换为对象成员 
	/*
	Ualais	 用户注册的昵称                            
	UimageSrc	用户的图片地址  请使用oss返回的地址                  
	UEmailLogin	   用户使用的邮箱地址                          
	Upasswd	    用户的密码建议使用sha1 加密后再上传  不要使用明文                                                           
	Uschool	  选填             
	                             
	*/
	//注意这里的 $userId 是 登录后获得的id 表名_所在行数
	public $Type;
	public $userId,$num; /*  这个拉取首页的     */
	
	/***
	这下面是 查看评论时候使用的节点属性  
	******/
	public $messageId,$refreshBool;
	public function __construct($dbName){    
	
			if(Choose_POST_GET){    //选择使用get还是post
			//*******
			   if(isset($_POST['userId'])){
				   $this->userId= $_POST['userId'];
			   }
			   if(isset($_POST['Type'])){
				   $this->Type= $_POST['Type'];
			   }
			   if(isset($_POST['num'])){
				   $this->num= $_POST['num'];
			   }
			   if(isset($_POST['messageId'])){
				   $this->messageId= $_POST['messageId'];
			   }   
			   if(isset($_POST['refreshBool'])){
				   $this->refreshBool= $_POST['refreshBool'];
			   }
			}else{ 
			//***
			   if(isset($_GET['userId'])){
				   $this->userId=$_GET['userId'];
			   }
			   if(isset($_GET['Type'])){
				   $this->Type=$_GET['Type'];
			   }
			   if(isset($_GET['num'])){
				   $this->num=$_GET['num'];
			   }
			   if(isset($_GET['messageId'])){
				   $this->messageId= $_GET['messageId'];
			   }
			   if(isset($_GET['refreshBool'])){
				   $this->refreshBool= $_GET['refreshBool'];
			   }
			}
       return parent::__construct($dbName);
	}



}
//初始化controller类 将POST和GET的都变成类成员 <上面if 语句控制接受那种数据>  
$controller1 = new getMessageViewController(DATABASE_NAME);

//初始化View  
$view =new getMessageView();

//初始化 viewModel 
$viewModel = new getMessageViewModel(DATABASE_NAME); //这是用户注册表的数据库  

if($controller1->Type==0){  // 接受信息     
	if($controller1->userId!=''&&$controller1->num!=''){  //这里不用检查type 在前面的if已经检查了  ,这里是主页获取信息的方法    
		if($controller1->num<1){ //页数不能小于1  要从第一页开始   
			echo $view->display_(['code'=>801]); //登录/查询失败   
		}else{// 页数检查成功  
			$viewModelResult = getMessageViewModel::getHomeMessageByUserId(
				$viewModel,
				$controller1->userId,
				$controller1->num
			);
			if($viewModelResult!=''){  //检查viewModel 访问数据库是否成功  
				echo $view->display_($viewModelResult);  //输出到视图  

			}else{
				echo $view->display_(['code'=>801,'error_msg'=>'查询失败']); //登录/查询失败   
			}
		}

	}else{
		echo $view->display_(['code'=>800,'error_msg'=>'漏填参数']);//漏填参数  
	}
}

if($controller1->Type== 1 ){
	if($controller1->userId!=''&&$controller1->num!=''&&$controller1->messageId){  //这里不用检查type 在前面的if已经检查了  ,这里是主页获取信息的方法   
		//手动刷新判断 
		if($controller1->refreshBool!=''){
			//不处理,直接传入函数,刷新静态文件   
			/* $viewModelResult = getMessageViewModel::getReplyMessageByUserId($viewModel,$controller1->userId,$controller1->messageId,$controller1->num,$controller1->refreshBool);  // 	function  */
			//现在使用 只有一层的评论  
			$viewModelResult = getMessageViewModel::getReplyMessageBy_OneLayer($viewModel,$controller1->userId,$controller1->messageId,$controller1->num,$controller1->refreshBool);  
		}else{
			//默认赋值,不刷新
			
			/* $viewModelResult = getMessageViewModel::getReplyMessageByUserId($viewModel,$controller1->userId,$controller1->messageId,$controller1->num);  // 这里缺失最后一个参数,会使用默认形参 */
			//现在使用 只有一层的评论  
			$viewModelResult = getMessageViewModel::getReplyMessageBy_OneLayer($viewModel,$controller1->userId,$controller1->messageId,$controller1->num);  
		}
		
			if($viewModelResult!=''){  //检查viewModel 访问数据库是否成功  
				echo $view->display_($viewModelResult);  //输出到视图  

			}else{
				
				echo $view->display_(['code'=>801,'error_msg'=>'查询失败']); //登录/查询失败   
			}
	}else{
		echo $view->display_(['code'=>800,'error_msg'=>'漏填参数']);//漏填参数  

	}
	
}

//图片列表的控制器 
if($controller1->Type== 2 ){
	if($controller1->userId!=''&&$controller1->messageId!=''){  //这里不用检查type 在前面的if已经检查了  ,这里是主页获取信息的方法    
		$viewModelResult = getMessageViewModel::getMessageToPhotoList($viewModel,$controller1->userId,$controller1->messageId);  // function getMessageToPhotoList($pdo,$userId,$messageId) ;
			if($viewModelResult!=''){  //检查viewModel 访问数据库是否成功  
				echo $view->display_($viewModelResult);  //输出到视图  

			}else{
				echo $view->display_(['code'=>801,'error_msg'=>'查询失败']); //登录/查询失败   
			}
	}else{
		echo $view->display_(['code'=>800,'error_msg'=>'漏填参数']);//漏填参数  

	}
	
}



?>