<?PHP
include_once("inc/common.inc.php");
$pageRight=RIGHT_NORMAL_USER;
include_once("inc/nav.inc.php");
include_once("inc/forms.inc.php");
include_once("inc/functions.inc.php");
include_once("inc/dbfunctions.inc.php");
dumpPostVars($_POST);
$pupilId=$_POST['pupilId'] ?? "";

if($pupilId==""){
	$pupilId=$_GET['pupilId'] ?? null;
}
$return=$_POST['return'] ?? null;
$borrow=$_POST['borrow'] ?? null;
$con=dbconnect();

if ($borrow!=null&&$borrow!=""){
	$toBorrow=$_POST['no'];
	$returnUntil=$_POST['returnUntil'];
	$toBorrow=beforeDB($con, $toBorrow);
	$returnUntil=datum_konvert($returnUntil);
	$sql = 'SELECT * FROM ausleihen as ausl '.
	'WHERE ausl.buchId='.$toBorrow.' AND ausl.zurueckgebracht=0 '.
	'LIMIT 0, 1';
	$borrowedResult=dbquery($con, $sql); 
	$sql = 'SELECT bu.id'.
		' FROM allebuecher as bu'.
		' where bu.id='.$toBorrow.' LIMIT 0, 1 ';
	$bookExists=dbquery($con, $sql); 	
	
	
	if(mysqli_num_rows($borrowedResult)>0){
		errorOut("Buch ist bereits ausgeliehen!");
	}else if(!mysqli_num_rows($bookExists)>0){
		errorOut("Kein Buch unter der Nummer $toBorrow gefunden!");
	}else{	
		//check input stuff
		$errors=checkAttribute($returnUntil, "", "Falsches Datum bei Zurückgeben bis");
		if ($errors!=""){
			errorOut($errors);
		}else{
			$sql = 'insert into `ausleihen` (`schuelerId`, `BuchId`, `von`, `bis`, `zurueckgebracht`) '
    	    	. ' values("'.$pupilId.'","'.$toBorrow.'","'.date("Y-m-d").'", "'.$returnUntil.'",0)';
    		dbquery($con, $sql); 
		}
	}
}elseif($return!=null&&$return!=""){
	$toReturn= $_POST['checkbox'];
	$rowCount= $_POST['rowCount'];
	$list="";
	for($i=0;$i<$rowCount;$i++){
		if(isset($toReturn[$i])){
			if($list==""){
				$list=$toReturn[$i];
			}else{
				$list.=", ".$toReturn[$i];	
			}
		}
	}

	if($list!=""){
		$sql= 'update `ausleihen` set `zurueckgebracht`=1, `zurueckAm`="'.date("Y-m-d").'" '
			. 'where id in('.$list.')';
		dbquery($con, $sql); 
	}
}
?>

		<h1>Ausleihe</h1>
<?php
openForm("borrowreturn.php");
$sql="SELECT id, name FROM alleschueler ORDER BY name";
$result=dbquery($con, $sql);
openTableRow();
selection("Schüler", "pupilId", $result, $pupilId, "document.forms[0].submit()", $con);
echo"
			<td>";
submitButton("anzeigen", "show");
echo"
			</td>";
closeTableRow();
closeForm();

echo"<br /><br />";
if($pupilId!=""&&$pupilId!=-1){
	$sql = 'SELECT ausl.id, bu.id, bu.reihe, bu.titel, ausl.von, ausl.bis '.
	'FROM ausleihen as ausl, allebuecher as bu '.
	'WHERE schuelerId='.$pupilId.' and ausl.buchId=bu.id and ausl.zurueckgebracht=0 '.
	'ORDER BY bis LIMIT 0, 30';
	$result=dbquery($con, $sql);

echo"
	<table class='list'>
		<form action='borrowreturn.php' method='POST'>	
";
openTableRow();
echo"
			<th>Zurück</th><th>Nr.</th><th>Reihe</th><th>Titel</th><th>von</th><th>bis</th>
";
closeTableRow();
$i=0;
while($menge=mysqli_fetch_row($result)){
    $menge=arrayAfterDB($menge, mysqli_num_fields($result));
	list ($borrowId, $bookId, $row, $bookTitle, $from, $to) = $menge;
    
    $from=datum_konvert($from);
    $to=datum_konvert($to);
    openChangeTableRow($i);
    echo"
    			<td class='list'>
    				<input type='checkbox' name='checkbox[$i]' value='$borrowId' id='id$i' />
    			</td>
    			<td class='list'>
    				$bookId
    			</td>
    			<td class='list'>
    				$row
    			</td>
    			<td class='list'>
    				$bookTitle
    			</td>
    			<td class='list'>
    				$from
    			</td>
    			<td class='list'>
    				$to
    			</td>
    ";
    
    closeTableRow();
    $i++;
}
if ($i==0){
	openTableRow();
	echo"
				<td colspan='6' class='bottom'>Keine ausgeliehenen Bücher</td>
	";
	closeTableRow();	
}else{
	openTableRow();
	echo"
	
					<td colspan='6' class='bottom'>
	";
	hiddenField("pupilId", $pupilId);
	hiddenField("rowCount", $i);
	submitButton("Zurückgeben", "return");
	echo"
					</td>";
	closeTableRow();
}
closeForm();
?>
<h2>Buch ausleihen</h2>
<?php
openForm("borrowreturn.php");
hiddenField("pupilId", $pupilId);
textFieldRow("Nr.", "no", "");
$date=getDateInDays(DAYS_TO_BORROW);
textFieldRow("Zurückgeben bis", "returnUntil", $date);
submitTableRow("Ausleihen", "borrow");
closeForm();
closebody();
}
?>