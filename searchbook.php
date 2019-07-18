<?PHP
include_once("inc/common.inc.php");

$pageRight=RIGHT_EVERYBODY;
include_once("inc/nav.inc.php");
include_once("inc/forms.inc.php");
include_once("inc/functions.inc.php");
include_once("inc/dbfunctions.inc.php");
include_once("inc/bookutils.inc.php");
include_once("inc/isbn/ISBN.php");

dumpVars($_GET, "GET VARS:");

$searchBook=$_POST['searchBook']?? "";
$showAllBooks=$_POST['showAllBooks'] ?? false;
$searchCriterion= $_POST['searchCriterion']?? "";
$selectedGruppe=getFromPostOrGet('selectedGruppe');
$selectedStandort=getFromPostOrGet('selectedStandort');
$selectedMedium=getFromPostOrGet('selectedMedium');
$orderId= $_GET['order'] ?? null;

$userRight=$_SESSION['userRight']?? "";

$sql=null;
//table headers
$headers = array(0 => array("order"=>"allebuecher.id", "title"=>"Nr"),
1 => array("order"=>"Kennung", "title"=>"Kennung"),
2 => array("order"=>"ISBN", "title"=>"ISBN"),
3 => array("order"=>"Medium", "title"=>"Medium"),
4 => array("order"=>"Autor", "title"=>"Autor"),
5 => array("order"=>"Reihe", "title"=>"Reihe"),
6 => array("order"=>"Titel", "title"=>"Titel"),
7 => array("order"=>"Gruppe", "title"=>"Gruppe"),
8 => array("order"=>"Schluesselwoerter", "title"=>"Schlüsselwörter"),
9 => array("order"=>"Standort", "title"=>"Standort"),
10 => array("order"=>"Ausgeliehen von", "title"=>"Name"),
11 => array("order"=>"bis", "title"=>"bis"),
);
$con=dbconnect();
if (!isEmpty($showAllBooks) || $orderId != null || !isEmpty($searchCriterion)) {
    $sql = 'SELECT allebuecher.id , `Kennung`, `ISBN`, `Medium` , `Autor`, `Reihe`, `Titel` , `Gruppe` , '
        . ' `Schluesselwoerter` , `Standort`, `alleschueler`.`Name` , `ausleihen`.`bis` '
        . ' FROM `allebuecher` left join `ausleihen` ON (allebuecher.id=buchId and zurueckgebracht=0) left join `alleschueler` '
        . ' ON (alleschueler.id=schuelerId) ';
    $wheres = array("Gruppe"=>$selectedGruppe, "Standort"=>$selectedStandort, "Medium"=>$selectedMedium);
	$additionalWhere = createWhere($con, $wheres);
	if (!isEmpty($showAllBooks) || $orderId!=null){//
		$ascdesc="ASC";
		if($orderId==null||$orderId < 0 || $orderId > count($headers)-1){
			$orderId=0;
		}else if($_GET['desc']==1){
			$ascdesc="DESC";
		}

		$all=true;
		$order=$headers[$orderId]["order"];
        $sql .= createWhereIfNecessary($additionalWhere)
            . ' ORDER BY ' . $order . ' ' . $ascdesc;
	}elseif(!isEmpty($searchCriterion)){
		/***************************************************************************
		 * ACHTUNG ACHTUNG
		 * The search result is empty because the word �MySQL� is present in at least 50%
		 * of the rows. As such, it is effectively treated as a stopword. For large datasets,
		 * this is the most desirable behavior: A natural language query should not return every
		 * second row from a 1GB table. For small datasets, it may be less desirable.
		 * ---> Immer 3 Rows haben!
		 ***************************************************************************/
		$all=false;
		$searchCriterion = beforeDB($con, $searchCriterion);

        $sql .= 'WHERE MATCH (`Titel` , `Schluesselwoerter`, `Autor`, `Reihe`) AGAINST (\'' . $searchCriterion . '\' ) '
            . createAndIfNecessary($additionalWhere);
	}
}

?>
<h1>Buch suchen</h1>
<p>Bitte Suchbegriff mit min. 4 Buchstaben eingeben. Durchsucht werden
Titel, Schlüsselwörter, Autor und Reihe eines Buches</p>
<?php
$groupResult=getGroupsFromDB($con);
$locationResult=getLocationsFromDB($con);
$mediaResult=getMediaFromDB($con);

openForm("searchbook.php");
textFieldRow("Suchbegriff", "searchCriterion", $searchCriterion);
$default = isEmpty($selectedGruppe) ? ALL : $selectedGruppe;

selectionAllRow("Gruppe", "selectedGruppe", $groupResult, false, true, $default);
$default = isEmpty($selectedStandort) ? ALL : $selectedStandort;
selectionAllRow("Standort", "selectedStandort", $locationResult, false, true, $default);
$default = isEmpty($selectedMedium) ? ALL : $selectedMedium;
selectionAllRow("Medium", "selectedMedium", $mediaResult, false, true, $default);
twoSubmitTableRow("Suchen", "Alle anzeigen", "searchBook", "showAllBooks");
closeForm();

if(!isEmpty($sql)){
	if(DEBUG){
		echo"$sql";
	}
	$result=dbquery($con, $sql);
	echo"	<table class='list'>";
	openTableRow();
	$link="searchbook.php?order=";
	foreach ($headers as $key => $element){
		echo"<th>";
		if($all){
			$additionalCriteria = "&selectedGruppe=$selectedGruppe&selectedMedium=$selectedMedium&selectedStandort=$selectedStandort";
			if($orderId==$key){
				if($ascdesc=="DESC"){
					$ascdescstring="asc=1";
					$arrow= "&darr;";
				}else{
					$ascdescstring="desc=1";
					$arrow= "&uarr;";
				}
				echo"<a href='".$link.$key."&$ascdescstring&$additionalCriteria'>";
				echo $element["title"];
				echo"</a>";
				echo$arrow;
			}else {
				echo"<a href='".$link.$key.$additionalCriteria."'>";
				echo $element["title"];
				echo"</a>";
			}
		}else{
			echo $element["title"];
		}
		echo"</th>\n";
	}


	closeTableRow();
	$i=0;
	while($menge=mysqli_fetch_row($result)){
		$menge=arrayAfterDB($menge, mysqli_num_fields($result));
		list ($id, $label, $isbn, $medium, $author, $row, $title, $group, $keywords, $location, $pupil, $returnUntil) = $menge;
		$returnUntil=datum_konvert($returnUntil);
		openChangeTableRow($i);
		echo"
	    			<td class='list'>
	    			$id
	    			</td>
	    			<td class='list'>
	    			$label
	    			</td>
	    			<td class='list'>
	    			".isbn_dashes($isbn)."
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
	    			$group
	    			</td>
	    			<td class='list'>
	    			$keywords
	    			</td>
				<td class='list'>
				$location
	    			</td>
				<td class='list'>";
				if($userRight>=RIGHT_NORMAL_USER){
					echo($pupil);
				}else{
					if($pupil!=""&&$pupil!=null){
						echo"ausgeliehen";
					}else{
						echo"&nbsp;";
					}
				}
				echo"
		  
	    			</td>
	    			<td class='list'>
	    			$returnUntil
	    			</td>
	    	
	    ";
	    			 
	    			closeTableRow();
	    			$i++;
	}
	openTableRow();
	if ($i==0){
		echo"
					<td colspan='".count($headers)."' class='bottom'>Keine Medien gefunden</td>
		";
	}else {
		echo"
					<td colspan='".count($headers)."' class='bottom'>$i Medien gefunden</td>
		";

	}
	closeTableRow();
	echo"	</table>";
}



closebody();


/**
 * creates a where-condition out of an array
 * @param $wheres AN ARRAY
 */
function createWhere($con, $wheres){
	dumpVars($wheres, "where:");
	$result = null;
	foreach ($wheres as $colName => $colValue) {
		if(!isEmpty($colValue) && ALL != $colValue){
			if(isEmpty($result)){
				$result.="`".beforeDB($con, $colName)."` = '".beforeDB($con, $colValue)."' ";	
			} else {
				$result.="AND `".beforeDB($con, $colName)."` = '".beforeDB($con, $colValue)."' "; 
			}
			
		}
	}
	return $result;
}
?>