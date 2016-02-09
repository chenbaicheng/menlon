<?php
//这里不写入与数据库有关的代码
//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'databaseAccessProxy_Model'.DIRECTORY_SEPARATOR.'medoo_returnPDO.php'; //class Tool

class viewControllerBaseClass extends medoo_returnPDO {

	public function __construct($dbName){ 
     
	 return parent::__construct($dbName);

	}
	
}

?>