<?php
/**
* This example illustrates best practice with regard to using MySQLi in multiple threads
*
* For convenience and simplicity it uses a Pool.
**/
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'databaseAccessProxy_Model'.DIRECTORY_SEPARATOR.'medoo_returnPDO.php'; //class Tool

require_once Tool::returnAbsoluteRouteString(['config','db.config.php']);

class Connect extends Worker {

    public function __construct($database =DATABASE_NAME ) {
        $this->hostname = DATABASE_SERVER;
        $this->username = DATABASE_USER;
        $this->password = DATABASE_PWD;
        $this->database = $database ;
        $this->port     = DATABASE_PORT;
    }
    
    public function getConnection() {
        if (!self::$link) {
            self::$link = new medoo_returnPDO(
				$this->database
			);
        }
        
        /* do some exception/error stuff here maybe */       
         
        return self::$link;
    }
    
    protected $hostname;
    protected $username;
    protected $password;
    protected $database;
    protected $port;
    
    /**
    * Note that the link is stored statically, which for pthreads, means thread local
    **/
    protected static $link;
}


class Query extends Threaded {
	
    public function __construct($sql,$option,$tableName='',$column=[],$whereOption=[]) {
		//echo $option;
        $this->sql = $sql;
		$this->tableName =$tableName;
		$this->column =$column;
		$this->option =$option;
		$this->whereOption = $whereOption;
    }
    
    public function run() {
		$pdo = $this->worker->getConnection();
		//echo $this->option;
		switch($this->option){
			case 'query':
				
				$result = $pdo->query($this->sql);
				$rows;
				if ($result) {			
					
				}
				//var_dump($result);
				$this->result = $result;
				
				break;
			case 'select':
				//echo '2sssssssssss';
				if($this->tableName==''||$this->column==[]){
					break ;
				}
				$result =$pdo->select($this->tableName,
					$this->column,
					$this->whereOption
					);
				//echo '2sssssssssss';
				//var_dump($result);
				$this->result = $result;
				

				break;
			case 'insert':
				break;
			case 'update':
				break;
			case 'delete':
				break;
			case 'replace':
				break;
			case 'get':
				break;
			case 'has':
				break;
			case 'count':
				break;
			case 'max':
				break;
			case 'min':
				break;
			case 'avg':
				break;
			case 'sum':
				break;
			default:
				$this->result ='error';
				break;
		}
        
      
    }
    
    public function getResult() {
        return $this->result;
    }
    
    protected $sql,$tableName,$column,$option,$whereOption;
    protected $result;
	
}

$pool = new Pool(POOLCACHE, "Connect", [DATABASE_NAME]);//第三个是参数传递 
//public function __construct($sql,$option='query',$tableName='',$column=[],$whereOption=[]) ;
$pool->submit
    (new Query("SELECT * FROM `t_user_info_3`;",'query'));
$pool->submit
    (new Query('SELECT * FROM `messageid_tid_relationship_24`;','query'));
$pool->submit
    (new Query("SELECT * FROM `messageid_tid_relationship_25`;",'query'));
$pool->submit
    (new Query("SELECT * FROM `messageid_tid_relationship_26`;",'query'));
$pool->submit
    (new Query("SELECT * FROM `messageid_tid_relationship_27`;",'query'));
$pool->submit
    (new Query('','select','messageid_tid_relationship_26','*'));


$pool->shutdown();
//end 

/* ::collect is used here for shorthand to dump query results */
$pool->collect(function($query){
	
	var_dump($done = $query->getResult());
     $GLOBALS['z']=1;
    return count($done); 
});
var_dump( $z);
?>
