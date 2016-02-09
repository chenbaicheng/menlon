<?php
//这里不写入与数据库有关的代码
//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'viewControllerBaseClass.php'; //class --- Tool


//引入 viewModel ,由controller 传入view 
require_once Tool::returnAbsoluteRouteString( ['controller','ViewModel','MessageManager','UserMessageManager','transferMessageViewModel.php'
]);
//引入View
require_once Tool::returnAbsoluteRouteString( ['view','MessageManager','UserMessageManager','transferMessageView.php'
]);



class transferMessageViewController extends viewControllerBaseClass{
		//function transferMessageByUserId($pdo,$fromUserId,$toUserId,$messageId,$insertArray);
	public $Tid,$id,$messageId,$fromUserId,$toUserId,$Ualais;
	public function __construct($dbName){    
	
			if(Choose_POST_GET){    //选择使用get还是post
			//*******
				if(isset($_POST['Tid'])){
				   $this->Tid= $_POST['Tid'];
				}
				if(isset($_POST['id'])){
				   $this->id= $_POST['id'];
				}
				if(isset($_POST['fromUserId'])){
				   $this->fromUserId= $_POST['fromUserId'];
				}
				if(isset($_POST['messageId'])){
				   $this->messageId= $_POST['messageId'];
				}
				if(isset($_POST['toUserId'])){
				   $this->toUserId= $_POST['toUserId'];
				}
				if(isset($_POST['Ualais'])){
				   $this->Ualais= $_POST['Ualais'];
				}

			}else{
			//***
				if(isset($_GET['Tid'])){
				   $this->Tid= $_GET['Tid'];
				}
				if(isset($_GET['id'])){
				   $this->id= $_GET['id'];
				}
				if(isset($_GET['fromUserId'])){
				   $this->fromUserId= $_GET['fromUserId'];
				}
				if(isset($_GET['toUserId'])){
				   $this->toUserId= $_GET['toUserId'];
				}
				if(isset($_GET['messageId'])){
				   $this->messageId= $_GET['messageId'];
				}
				if(isset($_GET['Ualais'])){
				   $this->Ualais= $_GET['Ualais'];
				}
			}
       return parent::__construct($dbName);
	}



}
//初始化controller类 将POST和GET的都变成类成员 <上面if 语句控制接受那种数据>  
$controller1 = new transferMessageViewController(DATABASE_NAME);

//初始化View  
$view =new transferMessageView();

//初始化 viewModel 
//注意这里  转发就是点赞  两个是同等效果  
$viewModel = new transferMessageViewModel(DATABASE_NAME); //这是用户注册表的数据库  

 //	function transferMessageByUserId($pdo,$fromUserId,$messageId,$Ualais){  //pdo要传入 这个类生成的pdo

	if($controller1->fromUserId!=''&&$controller1->toUserId!=''&&$controller1->messageId!=''&&$controller1->Ualais!=''&&$controller1->Tid!=''&&$controller1->id!=''){  //这里不用检查type 在前面的if已经检查了  ,这里是主页获取信息的方法    
		//function transferMessageByUserId($pdo,$fromUserId,$toUserId,$messageId,$insertArray);
			$viewModelResult = $viewModel->transferMessageByUserId(
				$viewModel,
				$controller1->Tid,
				$controller1->id,/*这个也是话题和消息表中 消息在此话题的行序号*/
				$controller1->fromUserId,
				$controller1->toUserId,
				$controller1->messageId,
				$controller1->Ualais
			);
			if($viewModelResult!=''){  //检查viewModel 访问数据库是否成功  
				echo $view->display_($viewModelResult);  //输出到视图  

			}else{
				echo $view->display_(['code'=>801,'error_msg'=>'查询失败']); //登录/查询失败   
			}
		

	}else{
		echo $view->display_(['code'=>800,'error_msg'=>'漏填参数']);//漏填参数  
	}







?>