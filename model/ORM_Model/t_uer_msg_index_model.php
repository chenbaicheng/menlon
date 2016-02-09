<?php
//这里不写入与数据库有关的代码
//***** 分离出  、ViewModel 层、Service 层、Storage 层
//*****  现在是 ObjectModel层  
//*****
//***************
/*
4.  用户消息索引表（t_uer_msg_index） ：

字段名称	字节数	类型	描述
User_id	4	uint32	用户编号（联合主键）

Author_id	4	uint32	消息发布者编号（可能是被关注者，也可能是自己）（联合主键）
Msg_id	4	uint32	消息编号(由消息发布者的msg_count自增)（联合主键）
Time_t	4	Uint32	发布时间（必须是消息元数据产生时间）
备注：此表就是当我们点击“我的首页”时拉取的消息列表，只是索引，Time_t对这些消息进行排序  

*/
class t_uer_msg_index_model{
	public $User_id,$Author_id,$Msg_id,$Time_t;
	public function __construct($result_row){
		$this->User_id=/**/;
		$this->Author_id=/**/;
		$this->Msg_id=/**/;
		$this->Time_t=/**/;

	}

}

?>