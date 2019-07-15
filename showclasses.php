<?PHP
include_once("inc/common.inc.php");
$pageRight=RIGHT_COUNTER;
include_once("inc/nav.inc.php");
include_once("inc/forms.inc.php");
include_once("inc/functions.inc.php");
include_once("inc/dbfunctions.inc.php");

$class=$_POST['class'] ?? null;
$sql=null;

if ($class!=null && $class!=""){
	if($class=="all"){
		$sql = 'SELECT `id`, `Name`, `Klasse` from `alleschueler` ORDER BY Klasse, Name';
	}else{ 
		$sql = 'SELECT `id`, `Name`, `Klasse` from `alleschueler` where `Klasse`="'.$class.'" ORDER BY Name';
	}
}
?>
		<h1>Klassen anzeigen</h1>
<?php
openForm("showclasses.php");
$con=dbconnect();
$classSQL="SELECT Klasse, Klasse FROM klassen ORDER BY Klasse";
$result=dbquery($con, $classSQL);

openTableRow();
selectionAll("Klasse", "class", $result, true, true, $class);

echo"
			<td>";
submitButton("Anzeigen", "show");
echo"
			</td>";
closeTableRow();
closeForm();

if($sql!=null){
	$result=dbquery($con, $sql);

	echo"	<table class='list'>";
	openTableRow();
	echo"
				<th>Name</th><th>Klasse</th>
	";
	closeTableRow();
	$i=0;
	while($menge=mysqli_fetch_row($result)){
	    $menge=arrayAfterDB($menge, mysqli_num_fields($result));
		list ($id, $name, $class) = $menge;

	    openChangeTableRow($i);
	    echo"
	    			<td class='list'>
	    				<a href='borrowreturn.php?pupilId=$id'>
	    					$name
	    				</a>
	    			</td>
	    			<td class='list'>
	    				$class
	    			</td>
	    ";
	    
	    closeTableRow();
	    $i++;
	}
	openTableRow();
	if ($i==0){
		echo"
					<td colspan='9' class='bottom'>Keine Schüler gefunden</td>
		";
	}else {	
		echo"
					<td colspan='9' class='bottom'>$i Schüler gefunden</td>
		";

	}
	closeTableRow();	
	echo"	</table>";
}



closebody();

?>