<?php
//DIRECTORY_SEPARATOR 是斜杠常量 ;$_SERVER['DOCUMENT_ROOT'] 是根目录 ,要兼容linux window
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'databaseAccessProxy_Model'.DIRECTORY_SEPARATOR.'tool.php';
class JSON_Array_Control extends Tool{
/*
final---用于类、方法前。 
final类---不可被继承。 
final方法---不可被覆盖。 
*/
final static  function JsonString_to_array($tmpJsonString){
    	
    	return json_decode($tmpJsonString,true);
    } 
final static  function array_to_json($tmpArray){
    /*这里 json encode 需要加上JSON_UNESCAPED_UNICODE , 停止转换为unicode ,否则中文会变成 \u****   */

    	return json_encode($tmpArray,JSON_UNESCAPED_UNICODE );    	
    
    //	return json_encode($tmpArray );

    }
final static  function array_push_and_ReturnJson($tmp,$pushContentText){ 
    if($tmp==0){
   

        array_push($tmp,$pushContentText);
        // array_shift($aa);

        return self::array_to_json($tmp);
    }else{
        array_push($tmp,$pushContentText);
        return self::array_to_json($tmp);

    }    
    
    return 0; // 这是错误输出 
}
    
}
/*         $myArray = array(); 
$myArray['sdsds拾稻穗的小姑娘'][0] = '文化发顺丰独食难肥0';
$myArray['sdsds拾稻穗的小姑娘'][1] = '文化发顺丰独食难肥1';
$myArray['sdsds拾稻穗的小姑娘'][2] = '文化发顺丰独食难肥2';

$myArray['sdsds拾稻穗的小姑娘'][3] = ['ss'=>'1','2','4','3'];
  echo JSON_Array_Control::array_to_json($myArray);



   echo JSON_Array_Control::array_push_and_ReturnJson($myArray,'湿哒哒');
 */

?>