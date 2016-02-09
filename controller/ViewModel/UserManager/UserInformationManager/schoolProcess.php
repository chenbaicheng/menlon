<?php
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'View.php'; //class Tool
class   schoolProcess extends View{
	
	public function __construct(){    
       
	}
	
	public function saveToFileByLocal(){
		
		$string = file_get_contents('school_first.json');
		$jsonArray = JSON_Array_Control::JsonString_to_array($string);
		$tmpArray=[];
		$idForSchool=1;
		foreach($jsonArray as $keyForProvinceName=>$province){
			//var_dump($keyForProvinceName);
			$tmpArray[$keyForProvinceName]=[];
			//要先帮 某某市 初始化一个数组
			foreach($province as $local){
				$tmpArray[$keyForProvinceName][$local['local']]=[]; //为某市 初始化一个数组 以便放入那个市的学校  
				
			}
			foreach($province as $keyForLocalName=>$local){
				//var_dump( $local['']);
				$tmpArray[$keyForProvinceName][$local['local']][$idForSchool]=$local['schoolName'];
				++$idForSchool;//因为需要学校作为索引 所以需要一个id ,在上传时,名字和id一起上传
			}
		}
		echo JSON_Array_Control::array_to_json($tmpArray);
		 //这里直接写入文件 ,返回值只是用来标志异常  
		return 0;
	}
}  

$schoolProcessobj =new schoolProcess();
$schoolProcessobj->saveToFileByLocal();
?>