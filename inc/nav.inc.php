<?php
include_once("header.inc.php");
include_once("checklogin.inc.php");	
checkLogin($pageRight);


$rights=0;
if(isset($_SESSION['userRight'])){
	$rights=$_SESSION['userRight'];
}
if(!isset($login)){
	//falls wir nicht von der login-seite kommen
	sendHeaders();
}
htmlheadout("");
echo"
<span class=\"nav\">";
$nav="";
if(hasRightToViewPage($rights, RIGHT_NORMAL_USER)){
	$nav=addMenuItem($nav, "borrowreturn.php", "Ausleihe", false);
}
if(hasRightToViewPage($rights,RIGHT_EVERYBODY))
$nav=addMenuItem($nav, "searchbook.php", "Suchen", false);
if(hasRightToViewPage($rights,RIGHT_COUNTER)){
$nav.=" | <a href=\"due.php\">fällige Bücher</a> |
	<a href=\"showclasses.php\">Klassen anzeigen</a> ||
	<a href=\"enterbook.php\">Buch eingeben</a> |
	<a href=\"enterpupil.php\">Schüler eingeben</a> |
	<a href=\"enterclass.php\">Klasse eingeben</a>";
}
if(hasRightToViewPage($rights,RIGHT_ADMIN)){
	$nav.=" || <a href=\"users.php\">Benutzer</a>";
	$nav.=" || <a href=\"statistics.php\">Statistik</a>";
}
if(isset($rights)&&$rights>0){
	$nav.=" || <a href=\"login.php?logout=true\">Ausloggen</a>";
}else{
	$nav.=" || <a href=\"login.php\">Einloggen</a>";
}
echo $nav;
?>
</span>

<?php
function addMenuItem($nav, $link, $label, $separator){
	if($nav!=""){
		if(!$separator){
			$nav.=" | ";
		}else{
			$nav.=" || ";
		}
			
	}
	$nav.="<a href=\"$link\">$label</a>";
	return $nav;
}

?>