<?php

class userPool{
	//通过获取uid  来判断用户是哪个数据库的 
   public function	getUserID($username){
	   //查询user_view表  这是由外视图创建
	   //获取uid的编号   由手机号+自增号组成  
	   //返回uid
   }
   //注册时候的验证 
   public function assignUserAccount($username,$userPhoneCall){
	   if(!$this->checkUserName_PhoneNumberIsExit($username,$userPhoneCall)){
		   //这里是不存在这个用户名和 手机号  
		   //创建 用户  插入 user_view表 
	   }else{
		  // 提示用户 修改手机或者账号名
	   }
   }
   
   protected function checkUserName_PhoneNumberIsExit($username,$userPhoneCall){
	   //检查user表   
	   //where select count(username,phoneNumber) from user_view  username='' or phoneNumber =''
	   //返回 0 不存在  1存在  
   }
   public function checkEmailIsExit(){
	   //因为手机才是唯一标识  所以这里不用检查  
   }
   

	
	
}

?>