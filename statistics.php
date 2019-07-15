<?PHP
include_once("inc/common.inc.php");
$pageRight=RIGHT_ADMIN;
include_once("inc/dbfunctions.inc.php");

include_once("inc/nav.inc.php");
include_once("inc/forms.inc.php");
include_once("inc/functions.inc.php");
?>
<h1>Statistik</h1>
<?php
//table headers
$headers = array(0 => array("order"=>"id", "title"=>"Nr"),
1 => array("order"=>"Autor", "title"=>"Autor"),
2 => array("order"=>"Titel", "title"=>"Titel"),
3 => array("order"=>"anzAusgeliehen", "title"=>"Wie oft ausgeliehen"),
);
//Statistiken löschen
$until = getFromPostOrGet('until');
$delete = $_POST['delete'] ?? "";
$confirmation = $_GET['confirmation'] ?? "";
if(!isset($_GET['order'])){
	$orderId = count($headers)-1;
} else {
	$orderId= $_GET['order'];
}
dumpPostVars($_POST);
$con = dbconnect();
if(!isEmpty($delete)){
	//display yes/no-dialog
	$title="Ausleihdaten löschen bestätigen";
	$text="Sollen wirklich alle Ausleihdaten vor dem $until gelöscht werden?";
	require_once("dialog.inc.php");
	yesOrNoDialog($title, $text, "until", $until, "statistics.php?confirmation=true");
}

if(!isEmpty($confirmation) && !isEmpty($until)){
	$until = datum_konvert($until);
	if($until){
		$sql = "DELETE FROM `ausleihen` \n"
		. "WHERE `zurueckgebracht` = 1 AND `zurueckAm` < '".$until."'";
		mysqli_query($con, $sql);
		if(!dberrorSql($con, $sql)){
			statusOut("Ausleihdaten wurden gelöscht.");
		}else {
			errorOut("Ausleihdaten konnten nicht gelöscht werden!");
		}
	} else {
		errorOut("Ungültiges Datum");
	}
}
$ascdesc="ASC";
if($orderId==null||$orderId < 0 || $orderId > count($headers)-1){
	$orderId=count($headers)-1;
}else if($_GET['desc'] ?? 0 ==1){
	$ascdesc="DESC";
}
if(DEBUG) echo("orderid = $orderId");
$order=$headers[$orderId]["order"];
$sql = "SELECT b.id as id, b.Autor, b.Titel, 0 as anzAusgeliehen FROM `allebuecher` as b \n"
. "WHERE NOT EXISTS (\n"
. "select * from `ausleihen` as a where a.BuchId = b.id)\n"
. "UNION\n"
. "SELECT a.BuchId as id, b.Autor, b.Titel, count(*) as anzAusgeliehen FROM `ausleihen` as a, `allebuecher` as b \n"
. "where b.id=a.buchId\n"
. "GROUP BY a.BuchId\n"
. ' ORDER BY '.$order.' '.$ascdesc;
$result=dbQuery($con, $sql);
echo"	<table class='list'>";
openTableRow();
$link="statistics.php?order=";
foreach ($headers as $key => $element){
	echo"<th>";
	if($orderId==$key){
		if($ascdesc=="DESC"){
			$ascdescstring="asc=1";
			$arrow= "&darr;";
		}else{
			$ascdescstring="desc=1";
			$arrow= "&uarr;";
		}
		echo"<a href='$link$key&$ascdescstring'>";
		echo $element["title"];
		echo"</a>";
		echo$arrow;
	}else {
		echo"<a href='".$link.$key."'>";
		echo $element["title"];
		echo"</a>";
	}

	echo"</th>\n";
}
closeTableRow();
$i=0;
while($menge=mysqli_fetch_row($result)){
	$menge=arrayAfterDB($menge, mysqli_num_fields($result));
	list ($id, $author, $title, $count) = $menge;
	openChangeTableRow($i);
	echo"
    			<td class='list'>
    				<a href='enterbook.php?bookId=$id&showButton=Anzeigen'>$id</a>
    			</td>
    			<td class='list'>
    			$author
    			</td>
    			<td class='list'>
    			$title
    			</td>	    
    			<td class='list'>
    			$count
    			</td>
    ";

    			closeTableRow();
    			$i++;
}
echo"	</table> <p>";
openForm("statistics.php");
textFieldSubmitRow("Ausleihdaten löschen bis", "until", getDateInDays(-365), "Löschen", "delete");
closeForm();
echo"</p>";
closebody();


?>