<?php
include_once("inc/forms.inc.php");
function checkLogin($pageRight){
	session_start();
	if(!isset($_SESSION['userRight'])){
		$userRight=0;
		if(!hasRightToViewPage($userRight, $pageRight)){
			header("Location: login.php");
			errorDie("Sie haben keine Zugriffsrechte für diese Seite!");
		}
	}else {
		$userRight=$_SESSION['userRight'];
		if(!hasRightToViewPage($userRight, $pageRight)){
			errorDie("Sie haben keine Zugriffsrechte für diese Seite!");
		}
	}
}


function hasRightToViewPage($userRight, $neededRight){
	if($userRight >= $neededRight){
		return true;
	}else{
		return false;
	}
}
?>