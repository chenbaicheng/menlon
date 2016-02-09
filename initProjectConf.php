<?php


require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'databaseAccessProxy_Model'.DIRECTORY_SEPARATOR.'medoo_returnPDO.php'; //class Tool

require_once Tool::returnAbsoluteRouteString(['config','db.config.php']);

class initProjectConf extends medoo_returnPDO {
	
	public function __construct($dataBaseName){
		$medoo_returnPDO='medoo_returnPDO';
		/*这里调用父类的构造函数  好像前面不能加 $this  虽然这个是成员变量   如果想父类对象保存到 子类的变量 记得 不要书写 $this->pdo 而是直接使用 $pdo = parent::__construct(); */
		//$pdo = $medoo_returnPDO::__construct($dataBaseName);  
		//上面也会出问题 所以只能直接返回,然后在需要使用的时候传入构造函数初始化好的对象  
      return  $medoo_returnPDO::__construct($dataBaseName);  
		//输入要链接的数据库  

	}
	
	public function init($a){
		// 还是不要支持自动删除数据库  避免删除了 原来的数据  给用户决定 
		/*  $a->query('DROP DATABASE IF EXISTS '.DATABASE_NAME);
		 $a->query('DROP DATABASE IF EXISTS '.ReplyDATABASE_NAME); */
		try{ 
        $a->query('CREATE DATABASE  '.DATABASE_NAME .'  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;');
		/* $a->query('CREATE DATABASE  '.SEARCH_DATABASE_NAME .'  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'); */
		}catch(PDOException $e){
			echo '提示内容:'.$e.'<br />';
		}
        echo '初始化了数据库: '.DATABASE_NAME;
	/* 	$a->query('  CREATE DATABASE  '.ReplyDATABASE_NAME .'  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;');
        echo '初始化了数据库: '.ReplyDATABASE_NAME; */

	}
	
	public function initOtherDependDatabase($databasePdo){
		try{
		//这是任务列表 
		/* $databasePdo->query('CREATE TABLE IF NOT EXISTS  '.MISSIONLIST.'(
			timestamp int not null ,
			fromUserId varchar(40) not null,
			toUserId varchar(40) not null,
			content varchar(150) not null,
			uuid char(40) not null,
			type tinyint not null default 0
			)ENGINE=MyISAM;');
		$databasePdo->query('create unique index '.MISSIONLIST.'_index  
				on missionList(timestamp ASC);
				create unique index '.MISSIONLIST.'_index2  
				on missionList(type);'); */
		/* 创建  标签labels表 ,还有创建索引   */
		$databasePdo->query('CREATE TABLE IF NOT EXISTS  lables(
		Lid int not null primary key  auto_increment,
		Lcontent varchar(16) not null 
		);');

		$databasePdo->query('create unique index Lcontent_allUser 
				on lables(Lcontent(16)); ');
		//创建话题表 ,这里只用一张表,因为话题不可能会出现几百万行.....
		$databasePdo->query('CREATE TABLE IF NOT EXISTS '.MessageTitle.'(
		Tid int not null auto_increment primary key ,
		Tnum int not null default 0 ,
		Tname varchar(20) not null unique,
		TmessageCount int not null default 0,
		TsubmitTime datetime not null,
		Tcontent varchar(200) not null
		);'); //TsubmitTime 是记录最近一个帖子的发布时间  TmessageCount 记录这个话题有多少个帖子 
		$databasePdo->query('create index messageTitleIndex on '.MessageTitle.'(
		TmessageCount DESC,TsubmitTime DESC
		);');
		
		//创建学校总表,用来统计学校的发帖数量和排名  
		$databasePdo->query('CREATE TABLE IF NOT EXISTS '.SCHOOLTITLE.'(
		rowId int not null auto_increment primary key ,
		schoolId int not null unique,
		schoolName varchar(40) not null unique,
		schoolMessageCount int not null default 0,
		submitTime datetime not null,
		content varchar(200) not null
		);'); //submitTime 是记录(最近一个 x)帖子的发布时间  TmessageCount 记录这个话题有多少个帖子 
		$databasePdo->query('create index messageTitleIndex on '.SCHOOLTITLE.'(
		schoolMessageCount DESC,submitTime DESC
		);');

		//注册缓冲表  ,放校验码 
		$databasePdo->query('CREATE TABLE IF NOT EXISTS  loginBuff(
		 UPhone varchar(20) not null,
		 CheckNumber int not null ,
		 time datetime not null,
		 primary key(UPhone)
		 ); ');
		$conditionCheck_step_by_step= $databasePdo->query('create index  loginBuffIndex on loginBuff(
		UPhone,CheckNumber
		)');
		if(!$conditionCheck_step_by_step){
			throw new PDOException('创建 loginBuff 表索引失败');
		}
		echo '索引 存储过程 , users 表 创建成功';
		}catch(Exception $e){
			echo '索引 存储过程 , users 表 创建失败'.'   '.$e;
		}
		
	}
	public static function drop_constrain_index($indexname,$tablename){
		$databasePdo->query('ALTER TABLE '.$tablename.' DROP INDEX '.$indexname.'; ');
		
	} 
	public  function __set($name,$value){
		$this->$name = $value;
	}
	public  function __get($name){
		return $this->$name;
	}
}

 $a=new initProjectConf('');
 $a->init($a);
 $a=new initProjectConf(DATABASE_NAME);
 $a->initOtherDependDatabase($a);
?>