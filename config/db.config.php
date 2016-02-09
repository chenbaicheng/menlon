<?php

// 调试模式  1是开启, 0是关闭  
define( 'DEBUG_condition' , 0 );
//调试的时候选择接受GET 还是POST  , 0为接受GET   
define('Choose_POST_GET',1);

define('STACK_CONDITION',0);

//********* 开始  这是user 数据库的设置  
define( 'DATABASE_TYPE' , 'mysql' );

// For MySQL, MariaDB, MSSQL, Sybase, PostgreSQL, Oracle
define( 'DATABASE_SERVER' , '127.0.0.1' );

define( 'DATABASE_USER' ,'root');

define( 'DATABASE_PWD' , '');

// For SQLite
//define( 'DATABASE_FILE' , '' );

// Optional
define( 'DATABASE_PORT' , '3306' );

define( 'DATABASE_CHARSET' , 'utf8' );

define( 'DATABASE_NAME' , 'imoment' );
//结束*************************


//********************************
//************ 下面是评论数据库的设置 ********************
//********************************

//********* 开始  这是user 数据库的设置  
define( 'ReplyDATABASE_TYPE' , 'mysql' );

// For MySQL, MariaDB, MSSQL, Sybase, PostgreSQL, Oracle
define( 'ReplyDATABASE_SERVER' , '127.0.0.1' );

define( 'ReplyDATABASE_USER' ,'root');

define( 'ReplyDATABASE_PWD' , '');

// For SQLite
//define( 'ReplyDATABASE_FILE' , '' );

// Optional
define( 'ReplyDATABASE_PORT' , '3306' );

define( 'ReplyDATABASE_CHARSET' , 'utf8' );

define( 'ReplyDATABASE_NAME' , 'reply' );