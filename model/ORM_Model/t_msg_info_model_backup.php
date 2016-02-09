<?php
require  '/core/medoo.php';
//***********这个是新浪云  $pdo = new PDO("mysql:host=".SAE_MYSQL_HOST_M.";port=".SAE_MYSQL_PORT.";dbname=".SAE_MYSQL_DB, SAE_MYSQL_USER, SAE_MYSQL_PASS);

$database = new medoo([
	// required
	'database_type' => 'mysql',
	'database_name' => SAE_MYSQL_DB,
	'server' => SAE_MYSQL_HOST_M,
	'username' =>  SAE_MYSQL_USER,
	'password' =>  SAE_MYSQL_PASS,
	'charset' => 'utf8',
 
	// [optional]
	'port' => SAE_MYSQL_PORT,
 
	// [optional] Table prefix
	'prefix' => 'PREFIX_',
 
	// driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
	'option' => [
		PDO::ATTR_CASE => PDO::CASE_NATURAL
	]
]);

$database->insert("account", [
	"user_name" => "foo",
	"email" => "foo@bar.com"
]);
}


?>