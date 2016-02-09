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
require_once Tool::returnAbsoluteRouteString( ['view','UserManager','UserInformationManager','userRegisterView.php'
]);




class userRegisterController extends viewControllerBaseClass{
    //在构造函数 接受所有GET,POST数据  将他们转换为对象成员 
	/*
	Ualais	 用户注册的昵称                            
	UimageSrc	用户的图片地址  请使用oss返回的地址                  
	UEmailLogin	   用户使用的邮箱地址                          
	Upasswd	    用户的密码建议使用sha1 加密后再上传  不要使用明文                                                           
	Uschool	  选填             
	                             
	*/
    public 	$Ualais,$UimageSrc,$UEmail,$Upasswd,$type,$Uid;
	public function __construct($dbName){ 
			if(0){  //选择使用get还是post
			   if(isset($_POST['Ualais'])){
				   $this->Ualais= $_POST['Ualais'];
			   }
			   if(isset($_POST['UimageSrc'])){
				   $this->UimageSrc= $_POST['UimageSrc'];
			   }
			   if(isset($_POST['UEmail'])){
				   $this->UEmail= $_POST['UEmail'];
			   }
			   if(isset($_POST['Upasswd'])){
				   $this->Upasswd= $_POST['Upasswd'];
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
				
			   if(isset($_GET['Ualais'])){
				   $this->Ualais= $_GET['Ualais'];
			   }

			   if(isset($_GET['UimageSrc'])){
				   $this->UimageSrc= $_GET['UimageSrc'];
			   }

			   if(isset($_GET['UEmail']) ){
				   $this->UEmail= $_GET['UEmail'];
			   }
			   
			  
			   if(isset($_GET['Upasswd']) ){
				   $this->Upasswd= $_GET['Upasswd'];
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
	$uid=sha1($obj->UEmail);
	
	$table_name = Tool::get_table_byTableCount('t_user_info_',$uid,10); //就是会分10张表 
/* 	echo 'uid:'.$uid.'<br />';
	echo $table_name; */
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

if($type_global =='check'){
	if($new1->UEmail!=''){
	//这个检查邮件的是否已被注册  ,传入邮箱,和pdo,还有表名
		
		$datas= $viewModel->checkUserEmailIsExist($new1->UEmail,$viewModel,$new1->getTableName($new1));
		
	    if($datas!=false){
		
		
			if(count($datas)==1){
				
			echo	$view->display_(['code'=>1]);
			}else{
				//返回没有此用户
			echo	$view->display_(['code'=>0]);

			}
		}
		echo	$view->display_(['code'=>0]);
		
	}
}

if( $type_global =='submit'){
   if($new1->Ualais!=''&&$new1->UimageSrc!=''&&$new1->Upasswd!=''&&$new1->UEmail!=''){
   //参数检查 成功后执行
		$viewModel_return = $viewModel->register(
		$new1->getTableName($new1),
		date('y-m-d h:i:s'),
		$new1->UEmail,
		$viewModel,[
		'uuid'=>sha1($new1->UEmail),
		'Ualais'=>$new1->Ualais,
		'Upasswd'=>$new1->Upasswd,
		'UimageSrc'=>$new1->UimageSrc,
		'UEmail'=>$new1->UEmail,
		'Udatetime'=>  date('y-m-d h:i:s')
		]);
		//var_dump( $viewModel_return);
		//@imoment  这里修改了medoo的源码  错误返回值为 '',正确为0
		if($viewModel_return!=''){
			echo $view->display_($viewModel_return);//输出成功信息
		}else{
			echo $view->display_(['code'=>1]);//输出失败信息
		}
   }else{
		echo $view->display_(['code'=>800]); //输出参数漏填信息
 
   }
    
   
}

?>