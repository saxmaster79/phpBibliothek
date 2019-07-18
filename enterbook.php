<?PHP
include_once("inc/common.inc.php");
$pageRight=RIGHT_COUNTER;
include_once("inc/nav.inc.php");
include_once("inc/forms.inc.php");
include_once("inc/functions.inc.php");
include_once("inc/dbfunctions.inc.php");
include_once("inc/bookutils.inc.php");
include_once("inc/isbn/ISBN.php");
$bookId=getFromPostOrGet('bookId') ?? "";
dumpPostVars($_POST);


$showButton=getFromPostOrGet('showButton') ?? "";
$saveButton=$_POST['saveButton'] ?? "";
$deleteButton=$_POST['deleteButton'] ?? "";

//init vars
$label=NULL;
$medium=NULL;
$author=NULL;
$row=NULL;
$zaehlung=NULL;
$isbn=NULL;
$title="";
$group="";
$location="";
$keyWords="";
$price=null;
$beschaffung=null;
$checked=false;

$con=dbconnect();

if(!isEmpty($showButton)){
		if ($bookId!=NULL && $bookId!=""){
			dbconnect();
			$sql = 'SELECT `id`, `Kennung`, `Medium`, `Autor`, `Reihe`, `Zählung`, `Titel`, `Gruppe`, `Schluesselwoerter`, `Standort`, `ISBN`, `Neupreis`, `Beschaffung` '.
			'FROM `allebuecher` '.
			'WHERE `id`="'.$bookId.'" LIMIT 0, 30';
			$result=dbquery($con, $sql);
			$menge=mysqli_fetch_array($result);
			$menge=arrayAfterDB($menge, mysqli_num_fields($result));

			list ($bookId, $label, $medium, $author, $row, $zaehlung, $title, $group, $keyWords, $location, $isbn, $price, $beschaffung) = $menge;
				$checked=$bookId;
				$label=afterDB($label);
				$medium=afterDB($medium);
				$author=afterDB($author);
				$row=afterDB($row);
				$title=afterDB($title);
				$group=afterDB($group);
				$keyWords=afterDB($keyWords);
				$location=afterDB($location);
				$isbn=isbn_dashes($isbn);
				$beschaffung=datum_konvert($beschaffung);
			$checked="";
		}else {
			errorOut("Bitte eine Buch-Nr. angeben!");
		}
}else if($saveButton==" Speichern "){
	$label=$_POST['label'];
	$medium=$_POST['medium'];
	$author=$_POST['author'];
	$row=$_POST['row'];
    $zaehlung=$_POST['zaehlung'];
	$title=$_POST['title'];
	$group=$_POST['group'];
	$location=$_POST['location'];
	$keyWords=$_POST['keyWords'];
	$checked=$_POST['checked'];
	$isbn=$_POST['isbn'];
    $price=$_POST['price'];
    $beschaffung=$_POST['beschaffung'];
	//TODO: Validierung richtig einbauen
	$errors="";
	$errors=checkAttribute($bookId, $errors, "Buch-Nr.");
	$errors=checkAttribute($author, $errors, "Autor");
	$errors=checkAttribute($title, $errors, "Titel");
	$errors=checkAttribute($location, $errors, "Standort");
	if(!isEmpty($isbn)){
		$errors=checkISBN($isbn, $errors, "ISBN");
	}
    $convertedBeschaffung=null;
	if(!isEmpty($beschaffung)){
        $convertedBeschaffung = datum_konvert($beschaffung);
	    $errors=checkAttribute($convertedBeschaffung, $errors, "Beschaffung");
    }
	if($errors!=""){
		$errors="Folgende Felder müssen ausgefüllt werden: ".$errors;
	}
	if(!preg_match("/^[0-9]+$/",$bookId)){
		$errors="Nr. muss eine Zahl sein. <br />\n".$errors;
	}

	if (!isEmpty($errors)){
		errorOut($errors);
	}else{
		$sql = 'SELECT `id`'.
		'FROM `allebuecher` '.
		'WHERE `id`='.$bookId.' LIMIT 0, 30';
		$result=dbquery($con, $sql);
			
		if(mysqli_num_rows($result)>0){
			if($checked!=$bookId){
				//ask user if update
				warningOut("Es existiert bereits ein Buch mit der Nr. ".$bookId."! Zum Überschreiben ".
				"&quot;Speichern&quot; drücken, sonst eine andere Nr. eingeben.");
				$checked=$bookId;
			}else{
					
				//update db
				$medium=beforeDB($con, $medium);
				$author=beforeDB($con, $author);
				$title=beforeDB($con, $title);
				$group=beforeDB($con, $group);
				$keyWords=beforeDB($con, $keyWords);
				$location=beforeDB($con, $location);
				$label=beforeDB($con, $label);
				$row=beforeDB($con, $row);
				$zaehlung=beforeDB($con, $zaehlung);
				$isbn=isbn_cleandashes($isbn);
				$price=beforeDB($con, $price);
                $beschaffung = $convertedBeschaffung;
                $sql = 'UPDATE `allebuecher` SET `Medium`="' . $medium . '", `Autor`="' . $author . '", `Reihe`="' . $row . '", `Zählung`="' . $zaehlung . '", `Kennung`="' . $label . '", ' .
                    '`Titel`="' . $title . '", `Gruppe`="' . $group . '", `Schluesselwoerter`="' . $keyWords . '", `Standort`="' . $location . '", `ISBN`="' . $isbn . '", '.
                    '`Neupreis`="' . $price . '", `Beschaffung`="' . $convertedBeschaffung . '"' .
                    'WHERE `id`="' . $bookId . '"';
				mysqli_query($con, $sql);
				if(dberrorSql($con, $sql)){
					die();
				}else{
					$checked="";
					$medium=afterDB($medium);
					$author=afterDB($author);
					$title=afterDB($title);
					$group=afterDB($group);
					$keyWords=afterDB($keyWords);
					$location=afterDB($location);
					$label=afterDB($label);
					$row=afterDB($row);
					$isbn=isbn_dashes($isbn);
					$beschaffung=datum_konvert($convertedBeschaffung);
					statusOut("Buch wurde aktualisiert");
				}
			}
		}else{
			//save to db
            $medium=beforeDB($con, $medium);
            $author=beforeDB($con, $author);
            $title=beforeDB($con, $title);
            $group=beforeDB($con, $group);
            $keyWords=beforeDB($con, $keyWords);
            $location=beforeDB($con, $location);
            $label=beforeDB($con, $label);
            $row=beforeDB($con, $row);
            $zaehlung=beforeDB($con, $zaehlung);
            $isbn=isbn_cleandashes($isbn);
            $price=beforeDB($con, $price);
            $beschaffung = $convertedBeschaffung;
			$sql='INSERT INTO `allebuecher` (`id`, `Medium`, `Autor`, `Reihe`, `Zählung`, `Titel`, `Kennung`, `Gruppe`, `Schluesselwoerter`, `Standort`, `ISBN`, `Neupreis`, `Beschaffung`) '.
			'VALUES ("'.$bookId.'", "'.$medium.'", "'.$author.'", "'.$row.'", "'.$zaehlung.'", "'.$title.'", "'.$label.'", "'.$group.'", "'.$keyWords.'", "'.$location.'", "'.$isbn.'", "'.$price.'", "'.$beschaffung.'")';
			mysqli_query($con, $sql);
			if(dberrorSql($con, $sql)){
				die();
			}else{
				statusOut("Buch wurde gespeichert");
				$bookId=null;
				$medium="Buch";
				$author="";
				$title="";
				$group="";
				$location="";
				$keyWords="";
				$row="";
                $zaehlung=null;
				$label="";
				$isbn=null;
				$beschaffung=null;
				$price=null;
			}
		}
	}
}elseif($deleteButton==" Löschen "){
	//Überprüfen ob das Buch noch ausgeliehen ist
	$sql = 'SELECT id FROM `ausleihen` WHERE BuchId="'.$bookId.'" AND zurueckgebracht="0" LIMIT 0, 30';

	$noOfBooks=mysqli_num_rows(dbQuery($con, $sql));
	if($noOfBooks>0){
		errorOut("Das Buch ist noch ausgeliehen! Es kann deshalb nicht gelöscht werden.");
	}else {
		//display yes/no-dialog
		$title="Buch löschen bestätigen";
		$text="Soll das Buch $bookId wirklich gelöscht werden?";
		require_once("dialog.inc.php");
		yesOrNoDialog($title, $text, "bookId", $bookId, "delete.php");
	}
}else{
	//Standardwerte für neues Buch:
	$medium="Buch";
	$checked="";
}

?>
<h1>Buch eingeben</h1>
<?php

$groupResult=getGroupsFromDB($con);
$locationResult=getLocationsFromDB($con);
$mediaResult=getMediaFromDB($con);

openForm("enterbook.php");
hiddenField("checked", $checked);
textFieldSubmitRow("Nr.", "bookId", $bookId, "Anzeigen", "showButton");
textFieldRow("Kennung", "label", $label);
selectionWithAdderRow("Medium", "medium", $mediaResult, $medium);
textFieldRow("Autor", "author", $author);
textFieldRow("Reihe", "row", $row);
textFieldRow("Zählung", "zaehlung", $zaehlung);
textFieldRow("Titel", "title", $title);
textFieldRow("ISBN", "isbn", $isbn);
textFieldRow("Neupreis", "price", $price);
textFieldRow("Datum Beschaffung", "beschaffung", $beschaffung);
selectionWithAdderRow("Gruppe", "group", $groupResult, $group);
selectionWithAdderRow("Standort", "location", $locationResult,  $location);
textAreaRow("Schlüsselwörter", "keyWords", $keyWords);
textDisplayRow("Hinweis", "Titel, Reihe und Autor müssen nicht erneut eingegeben werden.");
if($bookId==""){
	twoSubmitTableRowOneDisabled("Löschen", "Speichern", "deleteButton","saveButton",1);
}else{
	twoSubmitTableRow("Löschen", "Speichern", "deleteButton","saveButton");
}
closeForm();
closebody();
/**
 * @see http://www.blyberg.net/2006/04/05/php-port-of-isbn-1013-tool/
 *
 * @param unknown_type $isbn_no
 */
function checkISBN($isbn_no, $errormsg){
	$isbn_no = isbn_cleandashes($isbn_no);
	$isbntype = isbn_gettype($isbn_no);

	$isvalidten=false;
	
	if ($isbntype == 10) { $isvalidten = isbn_validateten($isbn_no); }
	else if ($isbntype == 13) { $isvalidttn = isbn_validatettn($isbn_no); }

	if (($isbntype < 1) || (!$isvalidten && !$isvalidttn)) { 
		$errormsg.="</br>Ungültige ISBN";
	}
	return $errormsg;
}
?>

