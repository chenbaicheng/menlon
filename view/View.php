<?php
require_once 'Json_encode_decode.php';
class View extends JSON_Array_Control
{
    public static function display(&$myArray){
//        ob_start();

		return JSON_Array_Control::array_to_json($myArray);
       // echo $output;
    }
}

//View::display();   