<?php
//这里不写入与数据库有关的代码
//***** 分离出  、ViewModel 层、Service 层、Storage 层
//*****  现在是 ObjectModel层  
//*****
//***************
/*
属性名称	数据类型	属性说明
Rid	Char(16)	评论编号
Mid	Char(16)	消息编号
Uid	Char(11)	用户编号
Rdatetime	Datetime	评论时间
Rcontent	Varchar(200)	评论内容
*/
class messageReply_model{
	public $rid,$mid,$uid,$Rdatetime,$Rcontent;
	public function __construct($result_row){
		$this->rid=/*消息(帖子)编号*/;
		$this->mid=/*用户编号*/;
		$this->uid=/*话题编号*/;
		$this->Rdatetime=/*转发编号*/;
		$this->Rcontent=/**/;
	


	}

	
	
}

?>