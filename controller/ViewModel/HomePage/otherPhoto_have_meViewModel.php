<?php

//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'ViewModel'.DIRECTORY_SEPARATOR.'viewModelBaseClass.php'; //class Tool


class otherPhoto_have_meViewModel extends  viewModelBaseClass{

	public function __construct($dbName){    
	    $parent='viewModelBaseClass';
      
		return $parent::__construct($dbName);
	}
	
}

?>