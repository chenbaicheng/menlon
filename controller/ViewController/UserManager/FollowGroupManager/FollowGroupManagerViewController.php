<?php
//这里不写入与数据库有关的代码
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'viewControllerBaseClass.php'; //class Tool


//引入 viewModel ,由controller 传入view 
require_once Tool::returnAbsoluteRouteString( ['controller','ViewModel','UserManager','FollowGroupManager','FollowGroupManagerViewModel.php'
]);
//引入View
require_once Tool::returnAbsoluteRouteString( ['view','UserManager','FollowGroupManager','FollowGroupManagerView.php'
]);




class FollowGroupManagerViewController extends viewControllerBaseClass{
    //在构造函数 接受所有GET,POST数据  将他们转换为对象成员 

    public 	$type,$forUserId,$forUalais,$toUserId,$toUalais,$groupName;
	public function __construct($dbName){ 
			if(Choose_POST_GET){  //选择使用get还是post
				if(isset($_POST['type'])){
					$this->type=$_POST['type'];
				}
				if(isset($_POST['forUserId'])){
					$this->forUserId=$_POST['forUserId'];
				}
				if(isset($_POST['forUalais'])){
					$this->forUalais=$_POST['forUalais'];
				}
				if(isset($_POST['toUserId'])){
					$this->forUserId=$_POST['toUserId'];
				}
				if(isset($_POST['toUalais'])){
					$this->toUalais=$_POST['toUalais'];
				}
				if(isset($_POST['groupName'])){
					$this->groupName=$_POST['groupName'];
				}
			}else{ 
				if(isset($_GET['type'])){
					$this->type=$_GET['type'];
				}
				if(isset($_GET['forUserId'])){
					$this->forUserId=$_GET['forUserId'];
				}
				if(isset($_GET['forUalais'])){
					$this->forUalais=$_GET['forUalais'];
				}
				if(isset($_GET['toUserId'])){
					$this->toUserId=$_GET['toUserId'];
				}
				if(isset($_GET['toUalais'])){
					$this->toUalais=$_GET['toUalais'];
				}
				if(isset($_GET['groupName'])){
					$this->groupName=$_GET['groupName'];
				}

			}
       return parent::__construct($dbName);
	}




}
//初始化controller类 将POST和GET的都变成类成员 <上面if 语句控制接受那种数据>  
$followGroupManagerViewControllerobj = new FollowGroupManagerViewController(DATABASE_NAME);
//获取步骤类型  
/* $type_global =$FollowGroupManagerViewControllerobj->type; */

//初始化viewModel
$viewModel = new FollowGroupManagerViewModel(DATABASE_NAME);

//初始化view 
$view = new FollowGroupManagerView();

//根据type 决定什么动作  **** 这里toUserId 是修改目标, forUserId是要修改表的后缀
if($followGroupManagerViewControllerobj->type =='editGroupName' ){
	if($followGroupManagerViewControllerobj->forUserId!=''&&$followGroupManagerViewControllerobj->toUserId!=''&&$followGroupManagerViewControllerobj->groupName!=''){
		$result = $viewModel->editFollowGroup($viewModel,$followGroupManagerViewControllerobj->forUserId,$followGroupManagerViewControllerobj->toUserId,$followGroupManagerViewControllerobj->groupName);
		if($result!=''){
			echo $view->display_($result);
		}else{
			echo $view->display_(['code'=>801,'error_msg'=>'服务器繁忙,请稍后再试']);
		}

	}else{
		echo $view->display_(['code'=>800,'error_msg'=>'漏填参数值']);
	}
}

//添加用户 
if($followGroupManagerViewControllerobj->type =='addUser' ){
	
	if($followGroupManagerViewControllerobj->forUserId!=''&&$followGroupManagerViewControllerobj->toUserId!=''&&$followGroupManagerViewControllerobj->toUalais!=''&&$followGroupManagerViewControllerobj->forUalais!=''){
		$insertArray=[/*toUserId 是要关注的用户, forUserId是点击关注按钮的用户*/
		'toUserId'=>$followGroupManagerViewControllerobj->toUserId,
		'toUalais'=>$followGroupManagerViewControllerobj->toUalais,
		'GroupName'=>$followGroupManagerViewControllerobj->groupName,
		'Type'=>1 /*关注类型(0,粉丝; 1,关注)*/
		];
		$insertArray2=[/*这是插入 所关注用户的那张表 */
		'toUserId'=>$followGroupManagerViewControllerobj->forUserId,/*这是粉丝id */
		'toUalais'=>$followGroupManagerViewControllerobj->forUalais,
		'GroupName'=>$followGroupManagerViewControllerobj->groupName,
		'Type'=>0
		];
		$result = $viewModel->addUser($viewModel,$followGroupManagerViewControllerobj->forUserId,$followGroupManagerViewControllerobj->toUserId,$insertArray,$insertArray2);
		if($result!=''){
			echo $view->display_($result);
		}else{
			echo $view->display_(['code'=>801,'error_msg'=>'服务器繁忙,请稍后再试']);
		}

	}else{
		echo $view->display_(['code'=>800,'error_msg'=>'漏填参数值']);
	}
}

//删除用户 
if($followGroupManagerViewControllerobj->type =='deleteUser' ){
	if($followGroupManagerViewControllerobj->forUserId!=''&&$followGroupManagerViewControllerobj->toUserId!=''){
		$result = $viewModel->deleteUser($viewModel,$followGroupManagerViewControllerobj->forUserId,$followGroupManagerViewControllerobj->toUserId);
		if($result!=''){
			echo $view->display_($result);
		}else{
			echo $view->display_(['code'=>801,'error_msg'=>'服务器繁忙,请稍后再试']);
		}

	}else{
		echo $view->display_(['code'=>800,'error_msg'=>'漏填参数值']);
	}
}

//获取好友列表
if($followGroupManagerViewControllerobj->type =='getUserList' ){
	if($followGroupManagerViewControllerobj->forUserId!=''){
		$result = $viewModel->getUserList($viewModel,$followGroupManagerViewControllerobj->forUserId);
		if($result!=''){
			echo $view->display_($result);
		}else{
			echo $view->display_(['code'=>801,'error_msg'=>'服务器繁忙,请稍后再试^_^']);
		}

	}else{
		echo $view->display_(['code'=>800,'error_msg'=>'漏填参数值']);
	}
}

?>