<?php
//这里不写入与数据库有关的代码
//***** 分离出  、ViewModel 层、Service 层、Storage 层
//*****  现在是 ObjectModel层  
//*****
//***************
/*
属性名称	数据类型	属性说明
Fid	Char(11)	收藏编号
Uid	Char(11)	用户编号(请求方)
Mid	Char(16)	消息编号(标识属于那个帖子)
*/
class messageReply_model{
	public $Fid,$Uid,$Mid;
	public function __construct($result_row){
		$this->Fid=/*消息(帖子)编号*/;
		$this->Uid=/*用户编号*/;
		$this->Mid=/*话题编号*/;

	}

	
	
}

?>