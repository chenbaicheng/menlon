<?php
//这里不写入与数据库有关的代码
//***** 分离出  、ViewModel 层、Service 层、Storage 层
//*****  现在是 objectModel层  
//*****
//***************
/*
属性名称	数据类型	属性说明
Mid	Char(16)	消息(帖子)编号
Uid	Char(11)	用户编号
Tid	Char(16)	话题编号
Cid	Char(11)	转发编号
Mcontent	Varchar(150)	在分享视频和图片时候的心情
Mhttp	Varchar(150)	链接(图片和视频)
Mfav	Int	收藏次数
Mcopy	Int	转发次数
Mdatetime	datetime	发表时间
		下面是后来附加的 sql语句还没有更改
Commented_count	  int	 评论次数(不受删除评论影响)
Comment_count	  int	 评论次数(删除评论后 他也会减1   )
Tyoe	tinyint	消息类型（0，原创；1，评论；2，转发）
*/
class t_msg_info_model{
	public $mid,$uid,$tid,$cid;
	public function __construct($result_row){
		$this->mid=/*消息(帖子)编号*/;
		$this->uid=/*用户编号*/;
		$this->tid=/*话题编号*/;
		$this->cid=/*转发编号*/;
		$this->Mcontent=/**/;
		$this->Mhttp=/**/;
		$this->Mfav=/**/;
		$this->Mcopy=/**/;
		$this->Mdatetime=/**/;
		$this->Commented_count=/**/;
		$this->Comment_count=/**/;
		$this->Tyoe=/**/;


	}

	
	
}

?>