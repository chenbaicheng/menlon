<?php
//这里不写入与数据库有关的代码
//***** 分离出  、ViewModel 层、Service 层、Storage 层
//*****  现在是 ObjectModel层  
//*****
//***************
/*
4. 用户使用标签UserLabel
属性名称	数据类型	属性说明
Uid	Char(11)	用户编号
Lid	char(11)	标签编号
*/
class Userlabel_model{
	public $Uid,$Lid;
	public function __construct($result_row){
		$this->Uid=/**/;
		$this->Lid=/**/;

	}

}

?>