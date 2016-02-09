<?php
//这里不写入与数据库有关的代码
//***** 
//***** 
//*****
//***************
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'viewControllerBaseClass.php'; //class --- Tool


//引入 viewModel ,由controller 传入view 
require_once Tool::returnAbsoluteRouteString( ['controller','ViewModel','MessageManager','UserMessageManager','sentMessageViewModel.php'
]);
//引入View
require_once Tool::returnAbsoluteRouteString( ['view','MessageManager','UserMessageManager','sentMessageView.php'
]);


class sentMessageViewController extends viewControllerBaseClass{
    //在构造函数 接受所有GET,POST数据  将他们转换为对象成员 
	/*
	Ualais	 用户注册的昵称                            
	UimageSrc	用户的图片地址  请使用oss返回的地址                  
	UEmailLogin	   用户使用的邮箱地址                          
	Upasswd	    用户的密码建议使用sha1 加密后再上传  不要使用明文                                                           
	Uschool	  选填             
	                             
	*/
	//注意这里的 $userId 是 登录后获得的id 表名_所在行数
    public 	$Type,$userId,$Tid,$Mcontent,$Mhttp,$ImgeWidth,$ImgeHeight,$Tname,$Tcontent;
	//下面是回帖的变量
	public $Ualais,$replyContent,$messageId ,$ForUalais,$ForUserId,$AtUserId,$AtUserName,$dependReplyId;
	//下面是记录经纬度 还有地名 ,extra 拓展字段
	public $location_x,$location_y,$locationName,$extra;
	public function __construct($dbName){ 
	
			if(Choose_POST_GET){  //选择使用get还是post
			//下面是记录经纬度 还有地名 ,extra 拓展字段
				if(isset($_POST['location_x'])){
				   $this->location_x= $_POST['location_x'];
				}
				if(isset($_POST['location_y'])){
				   $this->location_y= $_POST['location_y'];
				}
				if(isset($_POST['locationName'])){
				   $this->locationName= $_POST['locationName'];
				}
				if(isset($_POST['extra'])){
				   $this->extra= $_POST['extra'];
				}
			//评论
			   if(isset($_POST['replyContent'])){
				   $this->replyContent= $_POST['replyContent'];
			   }
			   if(isset($_POST['messageId'])){
				   $this->messageId= $_POST['messageId'];
			   }
			   if(isset($_POST['ForUalais'])){
				   $this->ForUalais= $_POST['ForUalais'];
			   }
			   if(isset($_POST['ForUserId'])){
				   $this->ForUserId= $_POST['ForUserId'];
			   }
			   if(isset($_POST['replyContent'])){
				   $this->replyContent= $_POST['replyContent'];
			   }
			//end
			   if(isset($_POST['userId'])){
				   $this->userId= $_POST['userId'];
			   }
			   
			   if(isset($_POST['Tid'])){
				   $this->Tid= $_POST['Tid'];
			   }
			   if(isset($_POST['Mcontent'])){
				   $this->Mcontent= $_POST['Mcontent'];
			   }
			   if(isset($_POST['Mhttp'])){
				   $this->Mhttp= $_POST['Mhttp'];
			   }
			   if(isset($_POST['ImgeWidth'])){
				   $this->ImgeWidth= $_POST['ImgeWidth'];
			   }
			   if(isset($_POST['ImgeHeight'])){
				   $this->ImgeHeight= $_POST['ImgeHeight'];
			   }
			   if(isset($_POST['Type'])){
				   $this->Type= $_POST['Type'];
			   }
			   if(isset($_POST['AtUserId'])){
				   $this->AtUserId= $_POST['AtUserId'];
			   }
			   if(isset($_POST['AtUserName'])){
				   $this->AtUserName= $_POST['AtUserName'];
			   }
			   if(isset($_POST['dependReplyId'])){
				   $this->dependReplyId= $_POST['dependReplyId'];
			   }
			   if(isset($_POST['Ualais'])){
				   $this->Ualais=$_POST['Ualais'];
			   }
			   
			   //这是话题的内容 
			   if(isset($_POST['Tname'])){
				   $this->Tname=$_POST['Tname'];
			   }
			   if(isset($_POST['Tcontent'])){
				   $this->Tcontent=$_POST['Tcontent'];
			   }
			}else{
				//下面是记录经纬度 还有地名 ,extra 拓展字段
				if(isset($_GET['location_x'])){
				   $this->location_x= $_GET['location_x'];
				}
				if(isset($_GET['location_y'])){
				   $this->location_y= $_GET['location_y'];
				}
				if(isset($_GET['locationName'])){
				   $this->locationName= $_GET['locationName'];
				}
				if(isset($_GET['extra'])){
				   $this->extra= $_GET['extra'];
				}
			//评论
			   if(isset($_GET['replyContent'])){
				   $this->replyContent= $_GET['replyContent'];
			   }
			   if(isset($_GET['messageId'])){
				   $this->messageId= $_GET['messageId'];
			   }
			   if(isset($_GET['ForUalais'])){
				   $this->ForUalais= $_GET['ForUalais'];
			   }
			   if(isset($_GET['ForUserId'])){
				   $this->ForUserId= $_GET['ForUserId'];
			   }
			   if(isset($_GET['replyContent'])){
				   $this->replyContent= $_GET['replyContent'];
			   }
			//end
			   if(isset($_GET['userId'])){
				   $this->userId= $_GET['userId'];
			   }
			   
			   if(isset($_GET['Tid'])){
				   $this->Tid= $_GET['Tid'];
			   }
			   if(isset($_GET['Mcontent'])){
				   $this->Mcontent= $_GET['Mcontent'];
			   }
			   if(isset($_GET['Mhttp'])){
				   $this->Mhttp= $_GET['Mhttp'];
			   }
			   if(isset($_GET['ImgeWidth'])){
				   $this->ImgeWidth= $_GET['ImgeWidth'];
			   }
			   if(isset($_GET['ImgeHeight'])){
				   $this->ImgeHeight= $_GET['ImgeHeight'];
			   }
			   if(isset($_GET['Type'])){
				   $this->Type= $_GET['Type'];
			   }
			   if(isset($_GET['AtUserId'])){
				   $this->AtUserId= $_GET['AtUserId'];
			   }
			   if(isset($_GET['AtUserName'])){
				   $this->AtUserName= $_GET['AtUserName'];
			   }
			   if(isset($_GET['dependReplyId'])){
				   $this->dependReplyId= $_GET['dependReplyId'];
			   }
			   if(isset($_GET['Ualais'])){
				   $this->Ualais=$_GET['Ualais'];
			   }
			   //这是话题的内容 
			   if(isset($_GET['Tname'])){
				   $this->Tname=$_GET['Tname'];
			   }
			   if(isset($_GET['Tcontent'])){
				   $this->Tcontent=$_GET['Tcontent'];
			   }
			}
       return parent::__construct($dbName);
	}



}
//初始化controller类 将POST和GET的都变成类成员 <上面if 语句控制接受那种数据>  
$controller1 = new sentMessageViewController(DATABASE_NAME);

//初始化View  
$view =new sentMessageView();

//初始化 viewModel 
$viewModel = new sentMessageViewModel(DATABASE_NAME); //这是用户注册表的数据库  

if($controller1->Type==0){//原创发信息  这里Tid 是发送消息的时候传入的 不用select取出tid
	if($controller1->userId!=''&&$controller1->ForUalais!=''&&$controller1->Mcontent!=''&&$controller1->Mhttp!=''&&$controller1->ImgeWidth!=''&&$controller1->ImgeHeight&&($controller1->Tid!=''||($controller1->Tname!=''&&$controller1->Tcontent!='') /*这里使用||号是因为 有两种情况,在原有话题发帖,或者新建一个话题发帖 */)){  //这里不用检查type 在前面的if已经检查了  ,这里是原创信息发布    
	//将 高宽 打包成数组   
		
		$ImgeWidthAndHeight_JSON= JSON_Array_Control::array_to_json(['width'=>$controller1->ImgeWidth,'height'=>$controller1->ImgeHeight]);
	//**************
		$viewModelResult='';
		$missValue=0;
		$insertArray=[
		'User_id'=>$controller1->userId,
		'ForUalais'=>$controller1->ForUalais,
		'Tid'=>$controller1->Tid,/*这个看viewModel 新话题插入数据库,会返回所在行数,也就是Tid的值,然后用Tid替换这个空值(旧Tid) */
		'Mcontent'=>$controller1->Mcontent,
		'Mhttp'=>$controller1->Mhttp,
		'ImgeWidthAndHeight_JSON'=>$ImgeWidthAndHeight_JSON,
		'location_x'=>$controller1->location_x,
		'location_y'=>$controller1->location_y,
		'locationName'=>$controller1->locationName,
		'extra'=>$controller1->extra, /*注意这里是json 所以输出的时候需要转换为数组*/
		'Type'=>$controller1->Type,
		'Time_t'=>TimeForChina
		];
		if($controller1->Tid!=''){ //就是因为话题编号存在 才说明话题已经有了 
			$viewModelResult = $viewModel->sentMessageByUserId($viewModel,$controller1->userId,$insertArray
			);
		}else if($controller1->Tid==''){ //这是插入新话题,就是因为话题编号存在 才说明话题已经有了   
			//echo date('y-m-d h:i:s',time());
			$viewModelResult = $viewModel->sentMessageByUserId($viewModel,$controller1->userId,$insertArray,[
			'Tname'=>$controller1->Tname,
			'Tcontent'=>$controller1->Tcontent
			]
			);
			//echo date('y-m-d h:i:s',time());
		}else{
			$missValue=1;
		}
		
		if($viewModelResult!=''){  //检查viewModel 访问数据库是否成功  
			echo $view->display_($viewModelResult);  //输出到视图  

		}else{
			if($missValue===1){
				echo $view->display_(['code'=>800,'error_msg'=>'话题编号和话题名称不能同时上传']);//漏填参数  
			}else{
				echo $view->display_(['code'=>801,'error_msg'=>'发布失败,请稍后再试']); //登录/查询失败   

			}
		}
	}else{
		echo $view->display_(['code'=>800,'error_msg'=>'参数填漏了 ']);//漏填参数  
	}
}
//*************发表评论    
if($controller1->Type == 1){  //B对A发出评论 , 评论记录在A身上 ,B只保留他的评论内容,A消息id,用户A id     
	//这里暂时不迁移到其他数据  
	if($controller1->userId!=''&&$controller1->messageId!=''&&$controller1->replyContent!=''&&$controller1->ForUalais!=''&&$controller1->ForUserId!=''){
		$insertArray=[
		'userId'=>$controller1->userId,/*发布消息(帖子)的用户id*/
		'messageId'=>$controller1->messageId,/*消息(帖子)ID*/
		'ForUalais'=>$controller1->ForUalais,/*回复消息(帖子)的用户昵称*/
		'ForUserId'=>$controller1->ForUserId,/*回复消息(帖子)的用户ID */
		'AtUserId'=>$controller1->AtUserId,/*要@的用户ID*/
		'AtUserName'=>$controller1->AtUserName,/*要@的用户昵称*/
		'dependReplyId'=>$controller1->dependReplyId,/*关联那个父消息Id*/
		'replyContent'=>$controller1->replyContent,/*回复的内容*/
		'replyDateTime'=>TimeForChina/*回复时间*/
		];
		$insertArray2=[ /* 注意当B用户点击曾经评论过的评论 ,只会跳转到总评论,而不会跳转到他评论哪里  */
		'userId'=>$controller1->userId,/*发布消息的用户id*/
		'messageId'=>$controller1->messageId,/* 消息ID*/
		'ForUalais'=>$controller1->ForUalais, /*回复消息的用户昵称*/
		'replyContent'=>$controller1->replyContent,/*回复的内容*/
		'replyDateTime'=>TimeForChina/*回复时间*/
		];
		$viewModelResult = $viewModel->sentReplyMessage($viewModel,$controller1->userId,$insertArray,$insertArray2);  //第一个数组 插入 replymsg_msg_A  第二个插入 replayMessageTo_B --B评论了什么记录在这里
		if($viewModelResult!=''){  //检查viewModel 访问数据库是否成功  
			echo $view->display_($viewModelResult);  //输出到视图  

		}else{
			echo $view->display_(['code'=>801,'error_msg'=>'发布评论失败,请稍后再试']); //登录/查询失败   
		}
	}else{
		echo $view->display_(['code'=>800]);//漏填参数  
	}
}

?>