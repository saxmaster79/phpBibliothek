<?php
/****
 * includes the real config file
 * relative to the including script :-(
 */
include_once("../../files/bibliothek/config.php");

/************************************/

if (!defined ("RIGHT_EVERYBODY")){
	define("RIGHT_EVERYBODY", 0);
	define("RIGHT_NORMAL_USER", 10);
	define("RIGHT_COUNTER", 20);
	define("RIGHT_ADMIN", 100);

	$rightArray=array(RIGHT_EVERYBODY=> "keine",
	RIGHT_NORMAL_USER=> "Lehrperson",
	RIGHT_COUNTER=> "Bibliothek verwalten",
	RIGHT_ADMIN=> "Administrator");

	$pageRight=20;

	if(DEBUG){
		error_reporting(E_ALL);
		//error_reporting(E_ALL ^ E_NOTICE);
	}else{
		error_reporting(E_NONE);
	}
}
?>