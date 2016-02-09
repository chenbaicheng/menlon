<?php
 //首先在消息与消息表中找出所有原评论
 // where denpendUserID=''
 //抽取 前面的5个原评论使用  ,   push进数组   
 
 //再根据原评论的id找出 ,匹配dependUserId            
 //将 command 键值  放入提取出来的评论array        
 
 /*
 差array_push();
 类似于
push进一个键值  [	['第一个原评论id',...,'comment'] ,
					['第二个原评论id',...,[ ['子评论'],
											[],
											[]
											]
											],
					[],
					[]
				]	
 */
 /* $array[0]['foruserName']=1;
 $array[0]['toUserName']=2;
 $array[0]['content']='sdsds';
array_unshift($array,['for'=>'jk','点对点'=>'dsfds']);
//array_unshift($array[],4,5,'sdadadas');
var_dump(array_shift($array)); */
 //var_dump($array);
foreach($_GET as $key=>&$data){
	$x=$_GET[$key];
	$_GET[$key]=12345;
	echo $key.':'.$data;
}

$redis = new Redis();  
$redis->connect('localhost',6379);  
$redis->set('long','Hello World');  
echo $redis->get('long');
?>