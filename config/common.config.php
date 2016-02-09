<?php
/******imoment 的token 设置 ******/
define('TOKENFROMSERVER','liaojuncheng');
/*******融云的任务队列**********/ 
define('POOLCACHE',10);
define('MISSIONLIST','missionList');
define('GETMISSSIONLISTCOUNT',1000);

//定义时间 24小时制 ,请在php.ini文件中设置好时区
define('TimeForChina',date('Y-m-d H:i:s',time()));
//******外部接口 appkey secretkey 的配置*********
// appkey  包含融云 和阿里云
// 融云的token
define('RONG_YUN_APPKEY','3argexb6rvz6e');
define('RONG_YUN_APPSECRET','wNU2tuvuWIv');

//阿里大鱼的短信  
define('DAYU_APPKEY','23282901');
define('DAYU_APPSECRET','dfc307a7b7d24fccf56b9d9c71cd66bb');
//外部接口配置结束 ******************


//******** 用户创建表格的前缀  注意表名之间不能留空格
//话题表
define('MessageTitle','messageTitle');

define('UserInformation','t_user_info_');
/* 4. 用户使用标签UserLabel_somebody */
define('UserLable_const', 'userLabel_' );

// 每页的数量  
define('MessagePageSize',9);  //每次提取消息的数量     
/* 1.普通消息message表<  t_msg_info  >：*/
define( 'Message_const','t_msg_info_' );
/*6. 皮肤skin_somebody表：*/
define( 'Skin_const' , 'skin_' );
/*1. 收藏fav_ to _somebody  表(就是点赞)：*/
define( 'Favour_const' , 'fav_MessageFrom_' ); //这里将From 改成To 好一点 
/*2. 关注表(  用户之间联系表(t_user_relation) --必须有关注与被关注的关系  ):*/
define( 'Relation_const' , 't_user_relation_' );
 /*
4.  用户消息索引表（t_uer_msg_index） ：
备注：此表就是当我们点击“我的首页”时拉取的消息列表，只是索引，Time_t对这些消息进行排序 */
define( 'MessageIndex_const' , 't_uer_msg_index_' );
// 用户数据库 表名  ---结束***********

//用户注册表的分表数量 ,请注意只有在第一次配置环境的时候才能更改,一旦配置完成,不能改动
define( 'Slice_Table' , 10 );   


//********************************
//************ 下面是评论数据库的设置 ********************
//********************************


//*** 评论表的设置
define( 'ReplyMessageTo' , 'replay_MessageTo_' );

//*****定义   帖子回复的一页的数量   **********
define('ReplyMessagePageSize',6);  // 评论一级目录
define('ReplyChildrenMessagePageSize',3); // 评论二级目录  
define( 'ReplyMsg_Msg' , 'replymsg_msg_' );
//定义只有一层评论的评论消息  
define('GetReplyMessageOnOneLayer',100);
//结束*************************

/*****************
****************************
		查找热度,用户 话题的数据表  
*****************************
*********************/

// 热度查找  每个用户表 提取多少个userId
//注意是分页的 总共有 #Slice_Table=10 张表 ,所以 只取一个就行,得到10个用户id,page控制找下一个用户
define('SEARCH_GET_USERID_COUNT_FOR_SEARCH',1);
//热门话题 每页有多少个话题 
define('HOTTITLECOUNT',10);

//从一个话题中 每页 取出多少个用户帖子  ,3的倍数  
define('GETMESSAGE_COUNT',12);
//话题 帖子联系表的名字   
define('MESSAGE_TID_RELATIONSHIP','messageId_Tid_relationship_');

//查找热度 用户 话题 数据库配置结束  

/**************
**** 按学校分类的查找      
**** 
****************/
define('SCHOOLTITLE','schoolTitle'); //记录学校发帖总数,还有校名,学校id ,这里会有一列id的自增列 用来备用
//下面使用学校id 作为后缀 
define('MESSAGE_SCHOOL_RELATIONSHIP','message_school_relationship_');

//热门学校  取出前12  4个一格刚刚好  
define('HOTSCHOOLCOUNT',12);
//学校分类数据配置结束  

/************
******查找 与 搜索模块 ****** 
************
***********/
//  取出前10个话题,取至话题列表 
define('FindManagerModule_TitleCOUNT',10);
//搜索昵称时取出的用户数量
define('FindManagerModule_UserCOUNT',10);
//  用户昵称索引表的分表数量  
define('UalaisIndexTableCount',20);
//  昵称索引表的表名 
define('UalaisIndexTableName','_UalaisIndex');

//查找 与 搜索模块  结束

/************
******动态模块 ****** 
************
***********/
define('GetFavourListCount',15);
//动态模块 结束
