<?php
//这里不写入与数据库有关的代码
//***** 
//***** 
//*****
//***************


class userAllPhotoView{

	public function __construct(){     .


	}
	public function display_($myArray){
		
		//调用父类的view输出
		$value = View::display($myArray);
		 return $value;  //这里输出要返回给客户端的json
	}
	
}

?>