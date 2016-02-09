<?php
//这里不写入与数据库有关的代码
//***** 分离出  、ViewModel 层、Service 层、Storage 层
//*****  现在是 ObjectModel层  
//*****
//***************
/*
5.  用户私信元信息表（t_uer_privilege_msg_to_someone） ：<注意私信和评论独立于两个不同系统  这里中文和英文都建议限制在150字以内  这是人脑能处理的最佳范围>  /**** 注意 这里只存放别人发给这个用户的信息 ,后期只要用Redis建立一个对象表 方便移植  A发送私信给B ,就是将私信写入t_uer_privilege_msg_to_B  这样每个用户只读取自己的表 **

字段名称	字节数	类型	描述
User_id	4	uint32	用户编号（接收方uid）
From_id	4	uint32	消息发布者编号（）（主键）
Content	Char(300)	char	消息内容
CreateTime_t	4	Uint32	发布时间（必须是消息元数据产生时间）
Img_Video_source	Vchar(255)	vchar	用来保存图片和视频的地址   
Msg_Content_Type	2	tinyint	消息内容类型: 0:文字信息 1:图片信息 2:视频信息 3:视频聊天请求  <这里不会考虑图片和文字一起发送>
备注：此表就是当我们点击“我的首页”时拉取的消息列表，只是索引，Time_t对这些消息进行排序
*/
class messageReply_model{
	public $User_id,$From_id,$Content,$CreateTime_t,$Img_Video_source,$Msg_Content_Type;
	public function __construct($result_row){
		$this->User_id=/**/;
		$this->From_id=/**/;
		$this->Content=/**/;
		$this->CreateTime_t=/**/;
		$this->Img_Video_source=/**/;
		$this->Msg_Content_Type=/**/;


	}

	
	
}

?>