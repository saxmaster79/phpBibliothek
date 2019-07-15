<?php
include_once("inc/common.inc.php");
$pageRight=RIGHT_COUNTER;
include_once("inc/forms.inc.php");
include_once("inc/nav.inc.php");
include_once("dialog.inc.php");
include_once("inc/dbfunctions.inc.php");
$userId=$_GET["user"] ?? null;
$yesButton=$_POST['yesButton'] ?? null;

htmlheadout("");
$currentUser = $_SESSION['username'];
if($userId==$currentUser){
	errorDie("Aktueller Benutzer kann nicht gelöscht werden!");
}

if($yesButton!=""){
	$con = dbconnect(); 
	$userId=$_POST['user'];
	if($userId!=""){
		$sql = 'DELETE FROM `benutzer` WHERE Name="'.$userId.'"';
		//echo $sql;
		mysqli_query($con, $sql);
		if(!dberror($con)){
			statusOut("Benutzer wurde gelöscht.");
		}else {
			errorOut("Benutzer konnte nicht gelöscht werden!");	
		}
	}
}else{
	$text="Soll der Benutzer $userId wirklich gelöscht werden?";
	yesOrNoDialog("Benutzer löschen bestätigen", $text, "user", $userId, "deleteuser.php");
}

?>