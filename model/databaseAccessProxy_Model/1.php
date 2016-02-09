<?php


require_once('medoo_returnPDO.php'); //class Tool



$dsn = Tool::returnDsnString();



$db = new PDO($dsn,DATABASE_USER, DATABASE_PWD);
 $db->exec("CREATE TABLE Persons
(
Id_P int,
LastName varchar(255),
FirstName varchar(255),
Address varchar(255),
City varchar(255)
)");

$database = new medoo_returnPDO('imoment');
 
// Enjoy
$database->insert('Persons', [
    'LastName' => 'foo',
    'FirstName' => 'foo@bar.com',
    'Address' => 25,
    'City' => 'zhaoqing'
]);
?>