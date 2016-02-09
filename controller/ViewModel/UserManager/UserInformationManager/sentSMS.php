<?php
	require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'viewControllerBaseClass.php'; 
	require_once Tool::returnAbsoluteRouteString(['controller','taobao-sdk-PHP',"TopSdk.php"]); 
	require_once Tool::returnAbsoluteRouteString(['view','Json_encode_decode.php']);
date_default_timezone_set('Asia/Shanghai'); 
//记录上传的手机  , 
class SentSMSForlogin{
	//这个根据用户的手机号码获取  ,用户表所在id  
	public  function getTableName($UPhone){
		$uid=sha1($UPhone);
		
		$table_name = Tool::get_table_byTableCount(UserInformation,$uid,Slice_Table); //就是会分10张表 ,根据常量的大小

		return $table_name;
	}
	//这个用了发送注册验证短信  
	private $UPhone,$randomValue;
	function phoneNumberCheck($pdo,$UPhone){
		$this->UPhone = $UPhone;
		//检查长度 ctype_digit：检测字符串中的字符是否都是数字，负数和小数会检测不通过 
		//长度超过11  不是纯数字字符串 都会被否决 
		if( (strlen($UPhone)!=11)||(!ctype_digit($UPhone) )){
			return ['code'=>801,'error_msg'=>'输入的手机不是11位']; //错误返回 
		}else{
			//检查是否手机已经存在 用户注册表里面,这样才能保证手机短信不重复发送
			try{
				try{
					$datas = $pdo->select($this->getTableName($this->UPhone),[
					'UPhone'
					],[
					'UPhone'=>$this->UPhone
					]);
					
					//因为上面try是捕获 用户表还没有创建的错误,现在是判断他如果有这张表, 有没有结果集返回
					if(!empty($datas)){
						//不为空就是说明 用户已经被注册,要返回 ''
						throw new PDOException('allReadyExist');
					}
				}catch(PDOException $exception){
					//Tool::debug_content( '使用正则表达式判断'.$exception->getMessage() );
					//不过一般不用,这里的try..catch只是用来捕捉 语法错误 
					//这里捕获以后并没有return  所以代码会继续执行下去 ,最后在这里判断字符串
					//是否 含有 Base table or view not found: 1146 Table 'imoment.t_user_info_1' doesn't exist

					//如果$datas不为空 捕捉用户,已经存在的错误
					if($exception->getMessage()=='allReadyExist'){
						//返回用户已经存在
						return ['code'=>801,'error_msg'=>'用户已经存在'];
					}
					//这里不处理 表不存在的错误(因为用户还没有提交注册信息,表还没有创建),让程序继续下去,

				}

				//---通过检查,这个手机没有在注册表, 继续检查缓冲表loginBff
				
				//在插入之前 先使用 select ,制作验证码失效的方法,检查这个用户的datetime 过期   
				//放弃这种方案  因为短信需要钱重发,所以采用一个月才清理一次 ,采用软件提示,或者推送 
				$datas = $pdo->select('loginBuff',[
				'CheckNumber'
				],[
				'UPhone'=>$UPhone
				]); //如果原本存在校验码

				if(!empty($datas)){ //如果存在验证码 就不再发送(省钱) 验证码请求,而是直接返回之前的验证码 
					$data1;
					foreach($datas as $data){
						$data1 =$data['CheckNumber'];
					}
					return ['code'=>201,'CheckNumber'=>$data1]; //201 已经存在校验码
				}

			}catch(PDOException $exception){
				Tool::debug_content( $exception->getMessage() );
				return ''; //错误返回 语法错误 未知错误
			}
			
			//下一步 如果注册表没有 缓冲表也没有 就开始发送短信,在短信发送成功以后 再将验证码插入缓冲表  			
			//***end
			//将手机和 验证码放入 缓冲表
			$this->randomValue= rand(100000,999999);

			//,开始发送短信  

			$c = new TopClient;
			$c->appkey =DAYU_APPKEY;
			$c->secretKey = DAYU_APPSECRET;
			$req = new AlibabaAliqinFcSmsNumSendRequest;
			$req->setExtend($this->randomValue);
			$req->setSmsType("normal");
			$req->setSmsFreeSignName("注册验证");
			//$req->setSmsParam('{"code":'.$this->randomValue.',"product":"imoment"}');
			 $req->setSmsParam('{"code":"'.$this->randomValue.'","product":"imoment"}'); 
			
			$req->setRecNum($UPhone);
			$req->setSmsTemplateCode("SMS_3140246");
			$resp = $c->execute($req);
	
		    
			//********
			//$arrayFromJson= JSON_Array_Control::JsonString_to_array($resp);
			//var_dump($resp);
			//检查状态 
			//if(!isset($resp->result->success)&&$resp->result->success!==true){
			if(!isset($resp->result->success)?false:true){
				//就是不成功 就返回
				if($resp->result->success!==true){
					return ['code'=>801,'error_msg'=>'请输入正确的手机号码'];
				}
			}else{
					return ['code'=>801,'error_msg'=>'发生错误 请输入正确的手机号码'];

			}
			//如果没有返回 就是成功发送出去了 
//只有短信发送成功才 插入表格
			$actionReturnValue = $pdo->action(function($pdo){
				try{

					$conditionCheck = $pdo->insert('loginBuff',[
					'UPhone'=>$this->UPhone,
					'CheckNumber'=>$this->randomValue,
					'time'=>date('y-m-d h:i:s',time())
					]);
					if($conditionCheck<0){
						throw new PDOException('fromUserId:'.$this->fromUserId.'  '.$pdo->last_query());
					}
				}catch(PDOException $exception){
						Tool::debug_content( $exception->getMessage() );
						return false;
				}
			});
			if($actionReturnValue!= -1){
				return ['code'=>200,'CheckNumber'=>$this->randomValue];   //这个是成功    
				//这里返回了用户的表名后缀  
				//这个是submit函数的返回值 
			}else{ //没有写入成功的 事务回滚的  
				return ''; //错误返回
			}
		
		}
		
	} //end function 
	
}

//然后将验证码 和手机保存在缓冲表中 
	
?>