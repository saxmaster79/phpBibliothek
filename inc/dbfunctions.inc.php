<?php

function dbconnect(){
	$con=new mysqli(DBHOST, DBLOGIN, DBPASS, DBNAME);
	echo "connected";
	if (mysqli_errno($con)!=0)
	die("Fehler bei der Datenbankverbindung");
	// DON'T TOUCH THISSSS
	mysqli_set_charset($con, 'utf8');
	return $con;
}
function dberror($connection){
	if (mysqli_errno($connection)!=0 && DEBUG){
		echo "<p>Fehler: ".mysqli_errno($connection).": ".mysqli_error($connection)."</p>";
		return true;
	}
	return false;
}
 function dberrorSql($connection, $sql){
	if (mysqli_errno($connection)!=0 && DEBUG){
		echo "<p>Fehler: ".mysqli_errno($connection).": ".mysqli_error($connection)."<br>
		$sql</p>";
		return true;
	}
	return false;
}

function dbQuery($connection, $sql){
	$result = $connection->query($sql);
	dberrorSql($connection, $sql);
	return $result;
}

function beforeDB($conn, $str){
	$ret=trim($str);
	$ret = strip_tags($ret);
	return mysqli_escape_string($conn, $ret);
}
function afterDB($str){
	$ret= stripslashes($str);
	return $ret;

}
 
function arrayAfterDB($array, $num_fields){
	//if (DEBUG) dumpVars($array, "beforeAfterDB:-)");

	if(count($array[0])==0) return $array;
	for($i=0;$i<$num_fields;$i++){
		$array[$i]=afterDB($array[$i]);
	}
	//if (DEBUG) dumpVars($array, "afterAfterDB:-)");
	return $array;
}
function createWhereIfNecessary($additionalWhere){
	if(!isEmpty($additionalWhere)){
		return "WHERE ".$additionalWhere;
	} else {
		return null;
	}
}
function createAndIfNecessary($additionalWhere){
	if(!isEmpty($additionalWhere)){
		return "AND ".$additionalWhere;
	} else {
		return null;
	}
}

function checkDbVersion() {
	$con=dbconnect();
	$sql = "select db.dbversion from dbversion db";
	$result = mysqli_fetch_row(dbQuery($con, $sql));
	if($result[0] != 2){
		errorDie("Falsche Datenbankversion: ".$result[0]." statt 2");
	}
}

?>