<?php
//

//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'ViewModel'.DIRECTORY_SEPARATOR.'viewModelBaseClass.php';

class FileUpLoadViewModel extends  viewModelBaseClass{

	public function __construct($dbName){    
	   // $parent='medoo_returnPDO'; //这里不要使用ViewModel基类 因为那时候还没有userId,token
		
		return parent::__construct($dbName);
	}

	static private function dealupLoadFile(&$file){
		$fileMd5 = md5($file["name"]);
		$file_anti_prefix = substr(strrchr($file["name"], '.'), 1);
		$newFileName =$fileMd5.'.'.$file_anti_prefix;
		if (file_exists("upload/" . $fileMd5)){
		
			return ['code'=>801,'error_msg'=>$file["name"].' already exists'];
		}else{
			move_uploaded_file($file["tmp_name"],
			"upload/".$newFileName );
			
			return ['code'=>200,'error_msg'=>Tool::returnAbsoluteWebRouteString([$_SERVER["HTTP_HOST"],'controller','ViewController','FileUpLoad','upload',$newFileName])];
		}
		return false;
		
	}
	
	static function fileUpload(&$file){
		if ($file["error"] > 0){
		
			return ['code'=>801,'error_msg'=>'Return Code:'.$file["error"]];

		}
		
		if (( $file["type"] == "image/gif")|| ($file["type"] == "image/jpeg")|| ($file["type"] == "image/pjpeg")||($file["type"] == "image/png") && ($file["size"] < 400*1000)){
			
			$resultArray =FileUpLoadViewModel::dealupLoadFile($file);
			if($resultArray === false){
				return false;
			}
			return $resultArray;	
		}else if($file["type"] == 'video/mpeg4'&&$file["size"] < 1000*5000 ){
			$resultArray =FileUpLoadViewModel::dealupLoadFile($file);
			if($resultArray === false){
				return false;
			}
			return $resultArray;	
		}else{
			//echo '{"code":801,"error_msg":"Invalid file"}';
			return ['code'=>801,'error_msg'=>'Invalid file'];
		}
		return false;
	}
	
	
}

?>