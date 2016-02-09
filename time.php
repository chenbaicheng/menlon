<?php 
/* ignore_user_abort(1); // run script in background 
set_time_limit(0); // run script forever 
$interval=3; // do every 15 minutes... 
$i=0;
$message_static_file = 'replyMessage_static_file.json'; 
do{ 
   // add the script that has to be ran every 15 minutes here 
   // ... 
   ++$i;
   file_put_contents($message_static_file,date('y-m-d h:i:s',time()));
   sleep($interval); // wait 15 minutes 
}while(true);  */
//unlink('replyMessage_static_file.json');

class AsyncOperation extends Thread {
  public function __construct($arg){
    $this->arg = $arg;
  }

  public function run(){
    if($this->arg){
      printf("Hello %s\n", $this->arg);
    }
  }
}
$thread = new AsyncOperation("World");
if($thread->start())
  $thread->join();

?> 