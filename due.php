<?PHP
include_once("inc/common.inc.php");
$pageRight=RIGHT_COUNTER;
include_once("inc/dbfunctions.inc.php");

include_once("inc/nav.inc.php");
include_once("inc/forms.inc.php");
include_once("inc/functions.inc.php");
?>
		<h1>Fällige Bücher</h1>
<?php
$today=date("Y-m-d");
$sql = 'SELECT alleschueler.Name, alleschueler.Klasse, allebuecher.id, `Medium` , `Autor`, `Reihe`, `Titel`, '
. ' bis '
. ' FROM `allebuecher`, `ausleihen`, `alleschueler` '
. ' where allebuecher.id=buchId and zurueckgebracht=0 and alleschueler.id=schuelerId'
. ' and bis < "'.$today.'"';
$con=dbconnect();	
$result=dbquery($con, $sql);

echo"	<table class='list'>";
openTableRow();
echo"		<th>Schüler</th><th>Klasse</th>
			<th>Nr</th><th>Medium</th><th>Autor</th>
			<th>Reihe</th><th>Titel</th>
			<th>zurück bis</td>
";
closeTableRow();
$i=0;
while($menge=mysqli_fetch_row($result)){
    $menge=arrayAfterDB($menge, mysqli_num_fields($result));
	list ($name, $class, $id, $medium, $author, $row, $title, $returnUntil) = $menge;
    $returnUntil=datum_konvert($returnUntil);
    openChangeTableRow($i);
    echo"
    			<td class='list'>
    				$name
    			</td>    			
    			<td class='list'>
    				$class
    			</td>
    			<td class='list'>
    				$id
    			</td>
    			<td class='list'>
    				$medium
    			</td>
    			<td class='list'>
    				$author
    			</td>
    			<td class='list'>
    				$row
    			</td>
    			<td class='list'>
    				$title
    			</td>	    
    			<td class='list'>
    				$returnUntil
    			</td>
    	
    ";
    
    closeTableRow();
    $i++;
}
if ($i==0){
	openTableRow();
	echo"
				<td colspan='5'>Keine fälligen Bücher</td>
	";
	closeTableRow();	
}
echo"	</table>";
closebody();

?>