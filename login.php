<?PHP
include_once("inc/forms.inc.php");
include_once("inc/dbfunctions.inc.php");
include_once("inc/common.inc.php");
include_once("inc/header.inc.php");
if(isset($_GET['logout'])){
	session_start();
	$_SESSION['userRight']=0;
	// Löschen aller Session-Variablen.
	$_SESSION = array();

	// Falls die Session gelöscht werden soll, löschen Sie auch das
	// Session-Cookie.
	// Achtung: Damit wird die Session gelöscht, nicht nur die Session-Daten!
	if (isset($_COOKIE[session_name()])) {
   		setcookie(session_name(), '', time()-42000, '/');
	}

	// Zum Schluss, löschen der Session.
	session_destroy();
}

$loggedIn=FALSE;
if(isset($_POST['login'])){
	$username=$_POST['asdf'];
	$passwd=$_POST['jkloe'];
	$loggedIn=initSession($username, $passwd);
}else{
	sendHeaders();
}
$login=true;
$pageRight=RIGHT_EVERYBODY;
include_once("inc/nav.inc.php");
echo"<h1>".TITLE."</h1>
<p>\n";
if($loggedIn){
	$msg="Sie sind eingeloggt";	
	statusOut($msg);	
}

if(!$loggedIn){
	openForm("login.php");
	textFieldRow("Benutzername", "asdf", "");
	passwordFieldRow("Passwort", "jkloe");
	submitTableRow("Einloggen", "login");
	closeForm();
}
echo"\n</p>\n";

closebody();

function initSession($username, $passwd){
	//echo "InitSession";
	$con=dbconnect();
	$username=beforeDB($con, $username);
	$passwd=beforeDB($con, $passwd);
	$query="Select Passwort, Salz FROM `benutzer` WHERE Name='$username'";
	$result=dbQuery($con, $query);
	list($stored_password, $salt)=mysqli_fetch_row($result);
    $encrypted_password = crypt($passwd, $salt);

    if ($stored_password==$encrypted_password) {
	/*	$SessionID=md5($User.time());
		$Zugriffszeit=date( ymdHi, time());
		mysql_query("UPDATE Zugriffsrechte
		          SET SessionID='$SessionID', Einlogzeit='$Zugriffszeit'
		          WHERE User='$User'");//Speicherung der Sitzungs-Identifikation
		Return $SessionID;*/
		session_start();
		sendHeaders();
		$query="Select Name, RealName, Rechte FROM benutzer WHERE Name='$username'";
		$result=dbQuery($con, $query);
		list($username, $realname, $userrights)=mysqli_fetch_row($result);
		$_SESSION['username']= $username;
		$_SESSION['realname']= $realname;
		$_SESSION['userRight']= $userrights;
		return true;
	}else{
		errorOut("Benutzername oder Passwort sind falsch!");
		return false;	
	}
}	

?>