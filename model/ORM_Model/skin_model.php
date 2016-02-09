<?php
//这里不写入与数据库有关的代码
//***** 分离出  、ViewModel 层、Service 层、Storage 层
//*****  现在是 ObjectModel层  
//*****
//***************
/*
6. 皮肤skin表：
属性名称	数据类型	属性说明
Sid 	Char(6)	皮肤编号
Simage	Varbinary(2048)	背景图片
Scolor	Char(9)	RGB主题颜色
*/
class skin_model{
	public $Uid,$Lid;
	public function __construct($result_row){
		$this->Uid=/**/;
		$this->Lid=/**/;

	}

}

?>