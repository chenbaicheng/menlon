<?php
//这里不写入与数据库有关的代码
//***** 分离出  、ViewModel 层、Service 层、Storage 层
//*****  现在是 ObjectModel层  
//*****
//***************
/* 
  
*/
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'View.php'; //class Tool
class   transferMessageView extends View{
	
	public function __construct(){    
       
	}
	
	public function display_($myArray){
		
		//调用父类的view输出
		$value = View::display($myArray);
		 return $value;  //这里输出要返回给客户端的json
	}
}  

?>