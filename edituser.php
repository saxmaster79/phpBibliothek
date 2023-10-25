<?PHP
include_once("inc/common.inc.php");
$pageRight=RIGHT_ADMIN;
include_once("inc/nav.inc.php");
include_once("inc/functions.inc.php");
include_once("inc/dbfunctions.inc.php");
include_once("inc/forms.inc.php");
dumpPostVars($_POST);
$con=dbconnect();
if(isset($_POST['action'])){
	$action=$_POST['action'];
	$username=$_POST['username'];
	$pw1=$_POST['qwert'];
	$pw2=$_POST['asdf'];
	$newUserRights=$_POST['newUserRights'];
	$realname=$_POST['realname'];
	
	$errors="";
	$errors=checkAttribute($username, $errors, "Benutzername.");
	$errors=checkAttribute($realname, $errors, "Name.");
		
	if($errors!=""){
		$errors="Folgende Felder müssen ausgefüllt werden: $errors";
	}
	
	if ($pw1!=$pw2){
		$errors="Passwörter sind nicht identisch! <br />\n".
				$errors;
	}
	if($action=="new"||$pw1!=""){
		if(strlen($pw1)<6){
			$errors="Passwort muss mindestens 6 Zeichen lang sein! <br />\n".
				$errors;
		}	
	}	
	if(!preg_match("/^[a-zA-Z0-9\.\-]+$/",$username)){
		$errors="Der Benutzername darf nur aus Buchstaben und Zahlen bestehen, Leerzeichen sind nicht erlaubt. <br />\n".
			$errors;
	}

	if($errors!=""){
		errorOut($errors);
	}else{
		
		$action=beforeDB($con, $action);
		$username=beforeDB($con,  $username);
		$newUserRights=beforeDB($con,  $newUserRights);
		if($newUserRights == "")
			$newUserRights = 0;
		$realname=beforeDB($con,  $realname);

		if($pw1!=""){
			$pw1=beforeDB($con,  $pw1);
			$salt_length=CRYPT_SALT_LENGTH;
			$jumble = md5(time() . getmypid());
			$salt = substr($jumble,0,$salt_length);
			$encrpass= crypt($pw1, $salt);
		}

		if($action=="new"){
			$query="INSERT INTO benutzer (Name, Passwort, Salz, Rechte, RealName) 
					VALUES ('$username', '$encrpass', '$salt', '$newUserRights', '$realname')";
		}elseif($action=="edit"){
			$query= "UPDATE benutzer SET Rechte='$newUserRights', RealName='$realname'";
				   	
			if(isset($encrpass)){
				$query.=", Passwort='$encrpass', Salz='$salt' ";
			}
			$query.="WHERE Name='$username'";
		}else{
			errorDie("unbekannte Action".$action);
		}
		mysqli_query($con, $query);
		if(dberror($con)){
			errorDie("Benutzer konnte nicht gespeichert werden!");
		}else{
			statusOut("Benutzer wurde gespeichert");
		}
	}
	
}else{
	$user=$_GET['user'] ?? "";
	if(isset($user)&&$user!=""){
		$user=beforeDB($con, $user);
		$sql = "SELECT `Name`, `Rechte`, `RealName` FROM `benutzer` ".
				"WHERE `Name`='$user'";
		list($username, $newUserRights, $realname)=mysqli_fetch_row(dbquery($con, $sql));
		$action="edit";
	}else{
		$action="new";
		$username="";
		$newUserRights="";
		$realname="";	
	}
}
echo "CRYPT_SALT_LENGTH".CRYPT_SALT_LENGTH;
echo"<h1>Benutzer </h1>";
openForm("edituser.php");
hiddenField("action", $action);
if($action!="new"){
	textDisplayRow("Benutzername", $username);
	hiddenField("username", $username);
}else{
	textFieldRow("Benutzername", "username", $username);
}
passwordFieldRow("Passwort", "qwert");
passwordFieldRow("Passwort wiederholen", "asdf");
selectionArrayRow("Rechte", "newUserRights", $rightArray, $newUserRights);
textFieldRow("Name", "realname", $realname);
submitCancelTableRow("Speichern", "submitButton");
closeForm();
closebody();
?>