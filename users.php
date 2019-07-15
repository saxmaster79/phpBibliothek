<?PHP
include_once("inc/common.inc.php");
$pageRight=RIGHT_ADMIN;
include_once("inc/nav.inc.php");
include_once("inc/functions.inc.php");
include_once("inc/dbfunctions.inc.php");
include_once("inc/forms.inc.php");

$query = 'SELECT `Name`, `Rechte`, `RealName` FROM `benutzer` LIMIT 0, 200';

?>
		<h1>Benutzer</h1>
		
<?php
$currentUser = $_SESSION['username'];
if($query!=null){
	//echo $sql;
	$con=dbconnect();	
	$result=dbQuery($con, $query);
	echo"	<table class='list'>";
	openTableRow();
?>
	<th>Benutzername</th>
	<th>Rechte</th>
	<th>Name</th>
	<th>&nbsp;</th>
<?php
	closeTableRow();
	$i=0;
	while($menge=mysqli_fetch_row($result)){
	    $menge=arrayAfterDB($menge, mysqli_num_fields($result));
		list ($name, $rights, $realname) = $menge;
	    openChangeTableRow($i);
	    echo"
	    			<td class='list'>
	    				<a href='edituser.php?user=$name'>$name</a>
	    			</td>
	    			<td class='list'>
						".$rightArray[$rights]."
	    			</td>
	    			<td class='list'>
	    				$realname
	    			</td>
					<td class='list'>";
	    if($name!=$currentUser){
	    echo"				<a href='deleteuser.php?user=$name'>löschen</a>";
	    }else{
	    echo"				<br/>";	
	    }
	    echo"
					</td>	    
	    ";
	    
	    closeTableRow();
	    $i++;
	}
	openTableRow();
	if ($i==0){
		echo"
					<td colspan='3' class='bottom'>Keine Benutzer gefunden</td>
		";
	}else {	
		echo"
					<td colspan='3' class='bottom'>$i Benutzer gefunden</td>
		";
	}
	echo"
					<td  class='bottom'><a href='edituser.php'>Benutzer hinzufügen</a></td>
	";
	closeTableRow();	
	echo"	</table>";
}



closebody();
?>