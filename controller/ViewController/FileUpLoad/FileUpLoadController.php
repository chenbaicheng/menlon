<?php

require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'viewControllerBaseClass.php'; //class Tool

require_once Tool::returnAbsoluteRouteString(['controller','ViewModel','FileUpLoad','FileUpLoadViewModel.php']);
require_once Tool::returnAbsoluteRouteString(['view','FileUpLoad','FileUpLoadView.php']);


class FileUpLoadController extends viewControllerBaseClass{
	public $file;
	public function __construct($dbName){ 
		if(isset($_FILES['file'])){
		   $this->file=$_FILES['file'];
		}
			
	}
	
	
}
//初始化view 
$view =new FileUpLoadView();
//初始化ViewModel 
$model = new FileUpLoadViewModel(DATABASE_NAME);
//初始化controller
$controller = new FileUpLoadController('');//因为不使用数据库  所以不连接

if($controller->file!=''){
	
	$returnValueArray	= $model->fileUpload($controller->file);
	if($returnValueArray!==false){
		echo $view->display_($returnValueArray);
	}else{
		echo $view->display_(['code'=>800,'error_msg'=>'无知的错误发生']); 

	}
}else{
	echo $view->display_(['code'=>800,'error_msg'=>'漏填参数']); //输出参数漏填信息
}


?>
