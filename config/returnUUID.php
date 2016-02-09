<?php

require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'uuid.php'; //class Tool
function returnUUID($UEmail){ //利用邮箱加uuid 保证唯一性  
$uuid =  Uuid::createV4();
$result1=str_replace('-','',$uuid);
$result =sha1 ($result1.$UEmail);
return $result; 
}
