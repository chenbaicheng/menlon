<?php
//这里不写入与数据库有关的代码
//***** 分离出  、ViewModel 层、Service 层、Storage 层
//*****  现在是 ObjectModel层  
//*****
//***************
/*
3. 标签label表
属性名称	数据类型	属性说明
Lid	Char(11)	标签编号
Lname	Varchar(10)	标签内容
*/
class label_model{
	public $Lid,$Lname;
	public function __construct($result_row){
		$this->Lid=/**/;
		$this->Lname=/**/;

	}

}

?>