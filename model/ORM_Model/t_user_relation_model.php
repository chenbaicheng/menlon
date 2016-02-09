<?php
//这里不写入与数据库有关的代码
//***** 分离出  、ViewModel 层、Service 层、Storage 层
//*****  现在是 ObjectModel层  
//*****
//***************
/*
2. 关注表attention表(t_user_relation ):
 
属性名称	数据类型	属性说明
Uid	Char(11)	用户编号
attUid	Char(11)	被关注者编号
Type	tinyint	关注类型(0,粉丝; 1,关注)
*/
class messageReply_model{
	public $Uid,$attUid,$Type;
	public function __construct($result_row){
		$this->Uid=/**/;
		$this->attUid=/**/;
		$this->Type=/**/;
	


	}

	
	
}

?>