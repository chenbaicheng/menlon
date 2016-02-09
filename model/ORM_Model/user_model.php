<?php
//这里不写入与数据库有关的代码
//***** 分离出  、ViewModel 层、Service 层、Storage 层
//*****  现在是 ObjectModel层  
//*****
//***************
/*
1. 用户user表:
属性名称	数据类型	属性说明
Uid	char(11)	用户编号
Ualais	varchar(20)	用户妮称
Uimage	Varchar(50)	用户头像 http地址
		
Ulogon	varchar(30)	邮箱（登陆名）
Upassswd	varchar(20)	密码
Usex	char(1)	性别
Uskin	char(11)	皮肤编号
Uschool	Char(11)	毕业院校
Utel	char(10)	手机号
Uinfo	varchar(100)	自我介绍
Udatetime	datetime	注册时间
  
*/
class messageReply_model{
	public $Uid,$Ualais,$Uimage,$Ulogon,$Upassswd,$Usex,$Uskin,$Uschool,$Utel,$Uinfo,$Udatetime;
	public function __construct($result_row){
		$this->Uid=/**/;
		$this->Ualais=/**/;
		$this->Uimage=/**/;
		$this->Ulogon=/**/;
		$this->Upassswd=/**/;
		$this->Usex=/**/;
		$this->Uskin=/**/;
		$this->Uschool=/**/;
		$this->Utel=/**/;
		$this->Uinfo=/**/;
		$this->Udatetime=/**/;

	}

}

?>