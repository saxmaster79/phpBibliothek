<?PHP
const SEARCH_ISBN = "Google Books abfragen";
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
$searchIsbnButton = $_POST['searchIsbnButton'] ?? "";

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
		if ($bookId){
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
} elseif($saveButton==" Speichern "){
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
				$zaehlung=beforeDBNonString($con, $zaehlung);
				$isbn=isbn_cleandashes($isbn);
				$price=beforeDBNonString($con, $price);
                $beschaffung = $convertedBeschaffung;

                $sql = 'UPDATE `allebuecher` SET `Medium`="' . $medium . '", `Autor`="' . $author . '", `Reihe`="' . $row . '", `Zählung`=' . $zaehlung . ', `Kennung`="' . $label . '", ' .
                    '`Titel`="' . $title . '", `Gruppe`="' . $group . '", `Schluesselwoerter`="' . $keyWords . '", `Standort`="' . $location . '", `ISBN`="' . $isbn . '", '.
                    '`Neupreis`=' . $price . ', `Beschaffung`=' . ($convertedBeschaffung ? '"'.$convertedBeschaffung.'"' : 'NULL') . ' ' .
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
                    $zaehlung = $zaehlung == "NULL" ? NULL : $zaehlung;
                    $isbn = isbn_dashes($isbn);
                    $beschaffung = datum_konvert($convertedBeschaffung);
                    $price = $price == "NULL" ? NULL : $price;
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
            $zaehlung=beforeDBNonString($con, $zaehlung);
            $isbn=isbn_cleandashes($isbn);
            $price=beforeDBNonString($con, $price);
            $beschaffung = $convertedBeschaffung;
			$sql='INSERT INTO `allebuecher` (`id`, `Medium`, `Autor`, `Reihe`, `Zählung`, `Titel`, `Kennung`, `Gruppe`, `Schluesselwoerter`, `Standort`, `ISBN`, `Neupreis`, `Beschaffung`) '.
			'VALUES ("'.$bookId.'", "'.$medium.'", "'.$author.'", "'.$row.'", '.$zaehlung.', "'.$title.'", "'.$label.'", "'.$group.'", "'.$keyWords.'", "'.$location.'", "'.$isbn.'", "'.$price.'", "'.$beschaffung.'")';
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
                $zaehlung="";
				$label="";
				$isbn="";
				$beschaffung="";
				$price="";
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
} elseif($searchIsbnButton== " ".SEARCH_ISBN." ") {
    try {
        $isbn=$_POST['isbn'];
        $isbnNoDashes=isbn_cleandashes($isbn);
        $googleApiUrl= "https://www.googleapis.com/books/v1/volumes?q=isbn:$isbnNoDashes&key=".GOOGLE_API_KEY;

        $string =  curl_get_contents($googleApiUrl);//getTestJSON();//
        $json_a = json_decode($string, true);
        $firstItem = $json_a['items'][0];

        $title = $firstItem['volumeInfo']['title'];
        $subTitle = $firstItem['volumeInfo']['subtitle'];
        if($subTitle)
            $title .= " - ".$subTitle;
        $authors = $firstItem['volumeInfo']['authors'];

        for ($i=0;$i< sizeof($authors); $i++){
            if($i==0){
                $author = reverseName($authors[$i]);
            } else {
                $author.="; ".reverseName($authors[$i]);
            }
        }

    } catch (Exception $e) {
        if(DEBUG)
            echo 'Exception abgefangen: ',  $e->getMessage(), "\n";
    }
}else{
    echo "nix selected";
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
textFieldRow("Signatur", "label", $label);
selectionWithAdderRow("Medium", "medium", $mediaResult, $medium);
textFieldRow("Autor", "author", $author);
textFieldRow("Reihe", "row", $row);
textFieldRow("Zählung", "zaehlung", $zaehlung);
textFieldRow("Titel", "title", $title);
textFieldSubmitRow("ISBN", "isbn", $isbn, SEARCH_ISBN, "searchIsbnButton");
textFieldRow("Neupreis", "price", $price);
textFieldRow("Datum Beschaffung", "beschaffung", $beschaffung);
selectionWithAdderRow("Gruppe", "group", $groupResult, $group);
selectionWithAdderRow("Standort", "location", $locationResult,  $location);
textAreaRow("Schlüsselwörter", "keyWords", $keyWords);
textDisplayRow("", "Titel, Reihe und Autor müssen nicht als Schlüsselwörter eingegeben werden.");
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

function reverseName($string) {
    if(!$string) return null;
    $arr = explode(' ', $string);
    $num = count($arr);
    if ($num > 1) {
        $name = $arr[$num-1].",";
        for ($i = 0; $i < $num -1; $i++){
            $name.=" ".$arr[$i];
        }
        return $name;
    } else {
        return $string;
    }
}

function curl_get_contents($url)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    $data = curl_exec($curl);
    curl_close($curl);
    return $data;
}

function getTestJSON(){
return "
{
 \"kind\": \"books#volumes\",
 \"totalItems\": 1,
 \"items\": [
  {
   \"kind\": \"books#volume\",
   \"id\": \"O4eYswEACAAJ\",
   \"etag\": \"xfmmSBMtNKc\",
   \"selfLink\": \"https://www.googleapis.com/books/v1/volumes/O4eYswEACAAJ\",
   \"volumeInfo\": {
    \"title\": \"Asterix in Italien\",
    \"authors\": [
     \"Jean-Yves Ferri\",
     \"Didier Conrad\"
    ],
    \"publishedDate\": \"2017-10-19\",
    \"description\": \"Asterix und Obelix begeben sich in die Höhle des Löwen! Ein turbulentes Abenteuer der Gallier im Italien der Antike.\",
    \"industryIdentifiers\": [
     {
      \"type\": \"ISBN_10\",
      \"identifier\": \"3770440374\"
     },
     {
      \"type\": \"ISBN_13\",
      \"identifier\": \"9783770440375\"
     }
    ],
    \"readingModes\": {
     \"text\": false,
     \"image\": false
    },
    \"pageCount\": 48,
    \"printType\": \"BOOK\",
    \"maturityRating\": \"NOT_MATURE\",
    \"allowAnonLogging\": false,
    \"contentVersion\": \"preview-1.0.0\",
    \"panelizationSummary\": {
     \"containsEpubBubbles\": false,
     \"containsImageBubbles\": false
    },
    \"imageLinks\": {
     \"smallThumbnail\": \"http://books.google.com/books/content?id=O4eYswEACAAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api\",
     \"thumbnail\": \"http://books.google.com/books/content?id=O4eYswEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api\"
    },
    \"language\": \"de\",
    \"previewLink\": \"http://books.google.ch/books?id=O4eYswEACAAJ&dq=isbn:9783770440375&hl=&cd=1&source=gbs_api\",
    \"infoLink\": \"http://books.google.ch/books?id=O4eYswEACAAJ&dq=isbn:9783770440375&hl=&source=gbs_api\",
    \"canonicalVolumeLink\": \"https://books.google.com/books/about/Asterix_in_Italien.html?hl=&id=O4eYswEACAAJ\"
   },
   \"saleInfo\": {
    \"country\": \"CH\",
    \"saleability\": \"NOT_FOR_SALE\",
    \"isEbook\": false
   },
   \"accessInfo\": {
    \"country\": \"CH\",
    \"viewability\": \"NO_PAGES\",
    \"embeddable\": false,
    \"publicDomain\": false,
    \"textToSpeechPermission\": \"ALLOWED\",
    \"epub\": {
     \"isAvailable\": false
    },
    \"pdf\": {
     \"isAvailable\": false
    },
    \"webReaderLink\": \"http://play.google.com/books/reader?id=O4eYswEACAAJ&hl=&printsec=frontcover&source=gbs_api\",
    \"accessViewStatus\": \"NONE\",
    \"quoteSharingAllowed\": false
   },
   \"searchInfo\": {
    \"textSnippet\": \"Asterix und Obelix nehmen an einem Wagenrennen quer durch Italien teil.\"
   }
  }
 ]
}

";

}
?>

