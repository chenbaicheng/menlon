<?php
//这里不写入与数据库有关的代码
//***** 分离出  、ViewModel 层、Service 层、Storage 层
//*****  现在是 ObjectModel层  
//*****
//***************
/*
3. 用户信息表（t_user_info）：
字段名称	字节数	类型	描述
User_id	4	uint32	用户编号（主键）
User_name	20	Char[20]	名称
Msg_count	4	uint32	发布消息数量,可以作为t_msg_info水平切分新表的auto_increment
Fans_count	4	uint32	粉丝数量
Follow_count	4	Uint32	关注对象数量
备注：以User_id取模分表
*/
class messageReply_model{
	public $User_id,$User_name,$Msg_count,$Fans_count,$Follow_count;
	public function __construct($result_row){
		$this->User_id=/**/;
		$this->User_name=/**/;
		$this->Msg_count=/**/;
		$this->Fans_count=/**/;
		$this->Follow_count=/**/;



	}

	
	
}

?>