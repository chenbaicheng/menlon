<?php
require_once 'medoo.php';


class medoo_returnPDO extends medoo{
	//这里的构造函数只要输入 数据库的名字 
	public function __construct($dname,$type=DATABASE_TYPE,$serverhost=DATABASE_SERVER,$username=DATABASE_USER,$password=DATABASE_PWD,$port=DATABASE_PORT,$charset=DATABASE_CHARSET){
		
		
		//上面 $charset 字符放最后面  是因为一般字符是不可能更改为其他编码  
		$med='medoo';
		// 或者parent::__construct(); 注意不要用new 关键字 ,还有建议不要使用parent ,因为父类有可能会发生改变 ,为保证稳定性,使用一个变量指定父类
		return $med::__construct([
/*     'database_type' => DATABASE_TYPE,
    'database_name' => $dname,
    'server' => DATABASE_SERVER,
    'username' => DATABASE_USER,
    'password' => DATABASE_PWD,
    'charset' => DATABASE_CHARSET,
	'port'=>DATABASE_PORT */
	'database_type' => $type,
    'database_name' => $dname,
    'server' => $serverhost,
    'username' => $username,
    'password' => $password,
    'charset' => $charset,
	'port'=>$port
]);
		
	}
	
}