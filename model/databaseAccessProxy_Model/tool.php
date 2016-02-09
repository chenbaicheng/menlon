<?php
/*****************************
//例如 网站目录在D:\imoment\1\Medoo-MVC-master  现在需要
//D:\imoment\1\Medoo-MVC-master\model\databaseAccessProxy_Model\db.config.php 文件  只需要在returnAbsoluteRouteString($array)传入数组['model','databaseAccessProxy_Model','db.config.php']   就会返回window 和linux通用的路径  
//
/******************************/
//不要删除DS 和WROOT 


define( 'DS' , DIRECTORY_SEPARATOR );
//define( 'WROOT' , dirname( __FILE__ ) . DS  );
define( 'WROOT' ,$_SERVER['DOCUMENT_ROOT'].DS);


//db 配置文件要和其他文件分离 因为服务器环境和调试环境的数据库配置不一致 
require_once Tool::returnAbsoluteRouteString(['config','db.config.php']); // same as inclue('webSize_Root/config/db.config.php')
require_once Tool::returnAbsoluteRouteString(['config','common.config.php']);

require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'uuid.php'; //  注意这里有命名空间


class Tool {
	 public function __construct(){
		 //在这里初始化 时区  如果这里不行 请在 Return_Pdo 函数 ,最好在php.ini设置
		 //http://zhidao.baidu.com/link?url=TnewH99BP2mii9W1XoBlXxahUL4MY7WO1OQmu03xZhRHlUZWFqEt4ahVHXOgvol8xtQX_XNE0MTQEIHiLvJk3a
//date_default_timezone_set("PRC");
	 }

	// returnAbsoluteRouteString() need constValuse WROOT   
	 static function convertStringToLinuxOrWindow_Route($str){
		$route='';
		if(preg_match("[/]",$str)&&(DS!='/')){ //DS是当前系统的斜杠形式
			$array=explode('/',$str);
			$route=Tool::convertArray($array);
			return $route;
		}else if(preg_match("[\]",$str)&&(DS!='\\')){
			$array=explode('\\',$str);
			$route=Tool::convertArray($array);
			return $route;
		}else{
			return $str;
		}
		return 1;
	}
	//用于网页上传的 
	static private function convertWebRouteArray($array){  /*private*/
		
		$route="";
		$i=0;
		foreach($array as $v){
			if($i<count($array)-1){  /* count()-1 是因为 如果有2个数组 在读到最后一个的时候不能 加上斜杠 所以要在到达最后一个之前 跳过附加斜杠的代码*/
				$route=$route.$v.'/';    
		/** 注意字符串 链接不能使用+ 号  只能显式链接 */
			}else{
				$route=$route.$v;
			}
			++$i;
		}
		return $route;
	}
	static function returnAbsoluteWebRouteString($array){
		//*********************
		//将输入的目录名和文件名数组转换为 路径 
		//用来兼容 linux和window的路径问题   
		//http://www.tanbo.name/html/99354.html
		//http://www.linux521.com/2009/system/200906/5484.html
		//***********************
		$route=Tool::convertWebRouteArray($array);// 将输入的目录名和文件名数组转换为 路径 
		//echo WROOT.$route;
		return $route;
	}
	
	static function returnAbsoluteRouteString($array){
		//*********************
		//将输入的目录名和文件名数组转换为 路径 
		//用来兼容 linux和window的路径问题   
		//http://www.tanbo.name/html/99354.html
		//http://www.linux521.com/2009/system/200906/5484.html
		//***********************
		$route=Tool::convertArray($array);// 将输入的目录名和文件名数组转换为 路径 
		//echo WROOT.$route;
		return WROOT.$route;
	}
	//echo WROOT.returnAbsoluteRouteString(['model','databaseAccessProxy_Model','medoo.php']);
	static function convertArray($array){  /*private*/
		
		$route="";
		$i=0;
		foreach($array as $v){
			if($i<count($array)-1){  /* count()-1 是因为 如果有2个数组 在读到最后一个的时候不能 加上斜杠 所以要在到达最后一个之前 跳过附加斜杠的代码*/
				$route=$route.$v.''.DS;    
		/** 注意字符串 链接不能使用+ 号  只能显式链接 */
			}else{
				$route=$route.''.$v;
			}
			++$i;
		}
		return $route;
	}


	// 将一个window路径的字符串转换为 linux使用的路径 ,能根据系统环境自动选择 

	static function returnDsnString(){
		//*****************************************
	//returnDsnString() need php file (db.config.php   常量都在那个文件)
		   return 	DATABASE_TYPE.":host=".DATABASE_SERVER.";port=".DATABASE_PORT.";dbname=".DATABASE_NAME.';charset=utf8;';
	}

		//下面的方法的$num则是分表总数   
		//***** 注意使用 这个用户注册表的专用函数,不要使用在其他表
	static function get_table_byTableCount($table_name,$uid,$num){ 
		   
	/* 	   return $uid >= 0 ? $table_name."_".(($uid%$num)+1):-1; */
		 return $uid >= 0 ? $table_name.(($uid%$num)+1):-1;
		}
		//这个是利用行数  下面的是每$num条数据分一次表
		//********注意这里的下划线  在常量定义中,不要在这里加入下划线    
	static function get_table_byRowCount($table_name,$uid,$num){ 
	/* 	   return $uid >= 0 ? $table_name."_".ceil($uid/$num):-1;

	 */
		   return $uid >= 0 ? $table_name.ceil($uid/$num):-1;
	 }
		//这个不要使用... ,这个和取模分表重复了 
	static function get_TableId($uuid,$num=Slice_Table){
		return ($uuid%10)+1;  //返回注册表号
	}
	//将用户userid 分割出 用户所在的表序号和所在行序号  
	static function slice_userId_ReturnArray($userId){
		return explode('_',$userId);
	}
	static function returnUUID($UEmail){ //利用邮箱加uuid 保证唯一性  
			$uuid =  Uuid::createV4();
			$result1=str_replace('-','',$uuid);
			$result =sha1 ($result1.$UEmail);
			return $result; 
	}
/* 	static function debug_Content($value,$content=''){
		if(DEBUG_condition){
		echo '<br />'.$content.':'.$value.'<br />';
		}
	}  */
	//使用对象   是因为这是在数据库改动时候写入文件
	 function debugOutput($value,$content='提示内容'){
		if(DEBUG_condition){
		echo '<br />'.$content.':'.$value.'<br />';
		}else{
			//这个模式是不在浏览器显示 而是记录在文件中  
			Tool::write_log($content.':'.$value);
		}
	 }
	static function debug_content($value,$content='提示内容'){
		if(DEBUG_condition){
		echo '<br />'.$content.':'.$value.'<br />';
		}else{
			//这个模式是不在浏览器显示 而是记录在文件中  
			Tool::write_log($content.':'.$value);
		}
	 }

    static private  function write_log($err) {
		
        $fh = fopen(Tool::returnAbsoluteRouteString(['log','log_'.date('Y-m-d',time()).'.txt']),'a'); // 追加方式打开,允许从后面追加内容  ,每天一个文档
        
        $err = "\r\n".date('Y-m-d H:i:s',time()) . "\r\n" . $err;
        fwrite($fh,$err);

        fclose($fh);
    }
	
 
	//class end
}


?>