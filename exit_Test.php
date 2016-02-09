<?php

//***** 
//***** 
//*****
//***************

class a{
	public function __construct(){
		echo 'a';
	}
}
class b extends a{
	public function __construct(){
		echo 'b';
		
		return parent::__construct();
	}
}

class c extends b{
/* 	public function __construct(){
		echo 'c';
		//exit('ssssss');
		return parent::__construct();
	} */
}
$a =new c();

?>