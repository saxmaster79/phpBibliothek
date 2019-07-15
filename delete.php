<?php
include_once("inc/common.inc.php");
$pageRight=RIGHT_COUNTER;
include_once("inc/forms.inc.php");
include_once("inc/nav.inc.php");
include_once("inc/dbfunctions.inc.php");
htmlheadout("");
$pupilId=$_POST['pupilId'] ?? "";
$bookId=$_POST['bookId'] ?? "";
$class=$_POST['class'] ?? "";

$yesButton=$_POST['yesButton'];
if($yesButton!=""){
	$con=dbconnect(); 
	if($pupilId!=""){
		$sql = 'DELETE FROM `ausleihen` WHERE SchuelerId="'.$pupilId.'"';
		//echo $sql;
		mysqli_query($con, $sql);
		$sql = 'DELETE FROM `alleschueler` WHERE id="'.$pupilId.'"';
		//echo $sql;
		mysqli_query($con, $sql);
		if(!dberror($con)){
			statusOut("Schüler wurde gelöscht.");
		}
		
	}elseif($bookId!=""){
		$sql = 'DELETE FROM `ausleihen` WHERE BuchId="'.$bookId.'"';
		//echo $sql;
		mysqli_query($con, $sql);
		$sql = 'DELETE FROM `allebuecher` WHERE id="'.$bookId.'"';
		//echo $sql;
		mysqli_query($con, $sql);
		if(!dberror($con)){
			statusOut("Buch wurde gelöscht.");
		}		
	}elseif($class!=""){
		$sql = 'DELETE FROM `klassen` WHERE Klasse="'.$class.'"';
		//echo $sql;
		mysqli_query($con, $sql);
		if(!dberror($con)){
			statusOut("Klasse wurde gelöscht.");
		}		
	}
}

?>