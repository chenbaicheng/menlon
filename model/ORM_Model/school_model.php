<?php
//这里不写入与数据库有关的代码
//***** 分离出  、ViewModel 层、Service 层、Storage 层
//*****  现在是 ObjectModel层  
//*****
//***************
/*
2. 院校school表
属性名称	数据类型	属性说明
Sid	Char(11)	院校编号
Sname	Varchar(10)	所在省
Spro	Varchar(10)	所在市
Scity	Varchar(10)	所在县
Scountry	Varchar(20)	院校名称
  
*/
class school_model{
	public $Sid,$Sname,$Spro,$Scity,$Scountry;
	public function __construct($result_row){
		$this->Sid=/**/;
		$this->Sname=/**/;
		$this->Spro=/**/;
		$this->Scity=/**/;
		$this->Scountry=/**/;


	}

}

?>