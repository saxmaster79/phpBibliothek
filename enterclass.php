<?PHP
include_once("inc/common.inc.php");
$pageRight=RIGHT_COUNTER;
include_once("inc/nav.inc.php");
include_once("inc/forms.inc.php");
include_once("inc/functions.inc.php");
include_once("inc/dbfunctions.inc.php");

//init vars
$checked=false;
$deleteClass=false;
$class=null;
$newClass=null;
$teacher=null;
$deleteButton = $_POST['deleteButton'] ?? null;
$showButton=$_POST['showButton'] ?? null;
$saveButton=$_POST['saveButton'] ?? null;
$class=$_POST['class'] ?? null;

dumpPostVars($_POST);
$con=dbconnect();
if($showButton!=""&&$showButton!=null){
	if ($class==null||$class==""){
		$class=$_GET['class'];
	}
	if ($class!=null&&$class!=""){
		$sql = 'SELECT `Klasse`,`Klassenlehrer` '.
				'FROM `klassen` '.
				'WHERE `Klasse`="'.$class.'" LIMIT 0, 30';
		$result=dbquery($con, $sql);

		$menge=mysqli_fetch_array($result);
		$menge=arrayAfterDB($menge, mysqli_num_fields($result));

		list ($class, $teacher) = $menge;
		$checked="";
	}else {
		errorOut("Bitte eine Klasse angeben!");
	}
}else if($saveButton==" Speichern "){
	$class=$_POST['class'];
	$newClass=$_POST['newClass'];
	$teacher=$_POST['teacher'];
	$deleteClass=$_POST['deleteClass'];
	$errors="";
	$errors=checkAttribute($class, $errors, "Klasse");
	$errors=checkAttribute($teacher, $errors, "Lehrperson");

	$sql = 'SELECT `Klasse` '.
					'FROM `klassen` '.
					'WHERE `Klasse`="'.$newClass.'" LIMIT 0, 30';

	$resultNewName=dbquery($con, $sql);
	if(mysqli_num_rows($resultNewName)>0){
		//ask user if merge classes
		$errors = "Es existiert bereits eine Klasse mit dem Namen ".$newClass."! Geben sie einen anderen Namen ein.";
	}

	if($errors!=""){
		errorOut("Folgende Felder müssen ausgefüllt werden: ".$errors);
	}else{
		$sql = 'SELECT `Klasse` '.
				'FROM `klassen` '.
				'WHERE `Klasse`="'.$class.'" LIMIT 0, 30';
		if(DEBUG){
			echo"SQL for old name: ".$sql;
		}
		$resultOldName=dbquery($con, $sql);
		if(mysqli_num_rows($resultOldName)>0){
			$id=mysqli_fetch_row($resultOldName);
			$id=$id[0];
			if($checked!=$id && isEmpty($newClass)){
				{
					//ask user if update
					warningOut("Es existiert bereits eine Klasse mit dem Namen ".$class."! Zum Aktualisieren der Klasse ".
						"&quot;Speichern&quot; drücken, sonst einen anderen Namen eingeben.");
				}
				$checked=$id;
				$class=afterDB($class);
				$newClass=afterDB($newClass);
				$teacher=afterDB($teacher);
			}else{
					
				//update db
				$class=beforeDB($con, $class);
				$newClass=beforeDB($con, $newClass);
				if($newClass==null||$newClass==""){
					$newClass=$class;
				}
				$teacher=beforeDB($con, $teacher);
				if($deleteClass==true){
					$sql='DELETE FROM `klassen` '.
							'WHERE `Klasse`="'.$class.'"';
				}else{
					$sql='UPDATE `klassen` SET `Klasse`="'.$newClass.'", `Klassenlehrer`="'.$teacher.'" '.
							'WHERE `Klasse`="'.$class.'"';
				}
				mysqli_query($con, $sql);
				$dberror=dberror($con);

				$sql = 'UPDATE `alleschueler` '
				. ' SET `Klasse`=\''.$newClass.'\''
				. ' WHERE `Klasse`=\''.$class.'\'';
				//echo"SQL: $sql";
				mysqli_query($con, $sql);
				$dberror&=dberror($con);
				if($dberror){
					die("Fehler beim Speichern!");
				}else{
					$checked="";
					$class=afterDB($newClass);
					$teacher=afterDB($teacher);
					$newClass=null;
					$deleteClass=false;
					statusOut("Klasse wurde aktualisiert");
				}
			}
		}else{
			//save to db
			$class=beforeDB($con, $class);
			$teacher=beforeDB($con, $teacher);

			$sql='INSERT INTO `klassen` (`Klasse`, `Klassenlehrer`) '.
				'VALUES ("'.$class.'", "'.$teacher.'")';
			mysqli_query($con, $sql);
			if(dberror($con)){
				die();
			}else{
				statusOut("Daten wurden gespeichert");
				$class="";
				$teacher="";
			}
		}
	}
}elseif($deleteButton==" Löschen "){
	//überprüfen ob der Klasse noch Schüler zugeordnet sind.
	dbconnect();
	$sql = 'SELECT id FROM `alleschueler` WHERE Klasse="'.$class.'" LIMIT 0, 50';

	$noOfPupils=mysqli_num_rows(mysqli_query($sql));
	if($noOfPupils>0){
		errorOut("Dieser Klasse sind $noOfPupils Schüler zugeordnet! Sie kann deshalb nicht gelöscht werden.");
	}else {
		//display yes/no-dialog
		$title="Klasse löschen bestätigen";
		$text="Soll die Klasse $class wirklich gelöscht werden?";
		require_once("dialog.inc.php");
		yesOrNoDialog($title, $text, "class", $class, "delete.php");
	}
}else{
	//Standardwerte für neue Klasse:
	$checked="";
	$newClass="";
	$deleteClass=false;
}

?>
<h1>Klasse</h1>
<?php
dbconnect();

openForm("enterclass.php");
hiddenField("checked", $checked);
hiddenField("deleteClass", $deleteClass);
textFieldSubmitRow("Klasse", "class", $class, "Anzeigen", "showButton");
textFieldRow("Klasse umbenennen", "newClass", $newClass);
textFieldRow("Lehrperson", "teacher", $teacher);
if($class==""){
	twoSubmitTableRowOneDisabled("Löschen", "Speichern", "deleteButton","saveButton",1);
}else{
	twoSubmitTableRow("Löschen", "Speichern", "deleteButton","saveButton");
}
closeForm();
closebody();
?>

