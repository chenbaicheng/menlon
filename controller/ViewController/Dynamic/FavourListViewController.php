<?php
//这里不写入与数据库有关的代码
//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'viewControllerBaseClass.php'; //class --- Tool


//引入 viewModel ,由controller 传入view 
require_once Tool::returnAbsoluteRouteString( ['controller','ViewModel','Dynamic','FavourListViewModel.php'
]);
//引入View
require_once Tool::returnAbsoluteRouteString( ['view','Dynamic','FavourListView.php'
]);


class FavourListViewController extends viewControllerBaseClass{
    //在构造函数 接受所有GET,POST数据  将他们转换为对象成员 

	//注意这里的 $userId 是 登录后获得的id 表名_所在行数
    public 	$type,$userId,$page;

	public function __construct($dbName){ 
	
			if(Choose_POST_GET){  //选择使用get还是post
			//下面是记录经纬度 还有地名 ,extra 拓展字段
				if(isset($_POST['userId'])){
				   $this->userId= $_POST['userId'];
				}
				if(isset($_POST['type'])){
				   $this->type= $_POST['type'];
				}
				if(isset($_POST['page'])){
				   $this->page= $_POST['page'];
				}
			}else{
			
				if(isset($_GET['userId'])){
				   $this->userId= $_GET['userId'];
				}
				if(isset($_GET['type'])){
				   $this->type= $_GET['type'];
				}
				if(isset($_GET['page'])){
				   $this->page= $_GET['page'];
				}
			}
       return parent::__construct($dbName);
	}



}
//初始化controller类 将POST和GET的都变成类成员 <上面if 语句控制接受那种数据>  
$controller1 = new FavourListViewController(DATABASE_NAME);

//初始化View  
$view =new FavourListView();

//初始化 viewModel 
$viewModel = new FavourListViewModel(DATABASE_NAME); //这是用户注册表的数据库  

if($controller1->type =='getFavourList'){
	if($controller1->userId!=''&&$controller1->page!=''){
		$result = FavourListViewModel::getFavourList($viewModel,$controller1->userId,$controller1->page);
	}else if($controller1->userId!=''&&$controller1->page==''){
		$result = FavourListViewModel::getFavourList($viewModel,$controller1->userId);
	}else{
		$result = ['code'=>800,'error_msg'=>'漏填参数'];
	}
	if($result!=''){
		echo $view->display_($result); //输出参数漏填信息
	}else{
		echo $view->display_(['code'=>801,'error_msg'=>'查询失败,请稍后再试']);//输出失败信息
	}
	
}

?>