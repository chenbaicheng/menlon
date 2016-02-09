<?php
//这里不写入与数据库有关的代码
//***** 分离出  、ViewModel 层、Service 层、Storage 层
//*****  现在是 ObjectModel层  
//*****
//***************
/*
2. 普通消息话题messageTitle表<类似于 #巴黎恐怖袭击#>
属性名称	数据类型	属性说明
Tid	Char(16)	话题编号
Tnum	Int	消息数量
Tname	Varchar(20)	话题标题
Tcontent	Varchar(1000)	话题简介
Ttime	datetime	发表时间
*/
class messageReply_model{
	public $Tid,$Tnum,$Tname,$Tcontent,$Ttime;
	public function __construct($result_row){
		$this->Tid=/**/;
		$this->Tnum=/**/;
		$this->Tname=/**/;
		$this->Tcontent=/**/;
		$this->Ttime=/**/;


	}

	
	
}

?>