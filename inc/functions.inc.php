<?php
// 0000-00-00 <-> 00.00.0000
function datum_konvert($ddmmyyyy){
	if(is_null($ddmmyyyy)) return false;
	if (strstr($ddmmyyyy, ".")){

        $date = date_create_from_format ( 'd.m.Y' , $ddmmyyyy );
		if (!$date) return false;
		return  date_format($date, 'Y-m-d');
	}
	elseif(strstr($ddmmyyyy, "-")){
		$Datum=explode("-", $ddmmyyyy);
		if (count($Datum)!=3) return false;
		$tag=trim($Datum[2]);
		$monat=trim($Datum[1]);
		$jahr=trim($Datum[0]);
		$jahr=datum_yyyy($jahr);
		$ok=checkdate($monat, $tag, $jahr);
		if ($ok)
		return $tag.".".$monat.".".$jahr;
		else
		return false;
	}
	//        if (DEBUG)
	//           echo "falsches Format $ddmmyyyy <br />";
	else return false;
}


function datum_yyyy($jahr){
	//falls das Jahr nur 2-stellig angegeben wird, werden
	//die letzten 2 Stellen ergänzt. Falls die 2-stellige
	//Jahreszahl grösser als das jetzige Jahr plus 5 ist,
	//dann gilt es als 19.., sonst als 20..-Jahr.
	if (strlen($jahr)==2){
		$Heute=getdate(time());
		$Heute=$Heute[year];
		if ($jahr>(substr($Heute,2,2)+5))$jahr="19".$jahr;
		else $jahr="20".$jahr;
	}
	return $jahr;
}
function checkAttribute($attribute, $concatenatedMsg, $attrname){
	if(DEBUG){
		echo"checking Attribute '$attribute' msg: '$concatenatedMsg', attrname: '$attrname'";
	}
	if(isEmpty($attribute)){
		if ($concatenatedMsg==""){
			$concatenatedMsg=$attrname;
		}else{
			$concatenatedMsg.=", ".$attrname;
		}
	}
	//also return previous errors
	return $concatenatedMsg;
}

/**
 * Schreibt einen Fehler, falls $attribute nicht leer und keine ganze Zahl ist
 */
function checkIntAttribute($attribute, $concatenatedMsg, $attrname){
    if(!isEmpty($attribute) && !ctype_digit($attribute)){
        if ($concatenatedMsg==""){
            $concatenatedMsg=$attrname;
        }else{
            $concatenatedMsg.=", ".$attrname;
        }
    }
    //also return previous errors
    return $concatenatedMsg;
}

/**
 * Schreibt einen Fehler, falls $attribute nicht leer und keine Decimal Zahl ist.
 */
function checkDecimalAttribute($attribute, $concatenatedMsg, $attrname){
    if(!isEmpty($attribute) && !is_numeric($attribute)){
        if ($concatenatedMsg==""){
            $concatenatedMsg=$attrname;
        }else{
            $concatenatedMsg.=", ".$attrname;
        }
    }
    //also return previous errors
    return $concatenatedMsg;
}

/**
 * Usage: dumpPostVars($_POST);
 */
function dumpPostVars($HTTP_POST_VARS) {
	dumpVars($HTTP_POST_VARS, "Values submitted via POST method:");
}
function dumpVars($arr, $text){
	if(DEBUG){
		echo "<p>$text<br>";
		//reset ($HTTP_POST_VARS);

            foreach ($arr as $key => $value) {
                echo "$key => $value<br>";
            }
		echo"</p>";
	}
}
function getFromPostOrGet($key){
	if (isset($_POST[$key]) && $_POST[$key]!=""){
		return $var=$_POST[$key];
	} else {
		return $_GET[$key] ?? "";
	}
}

/**
 * @param $string
 * @return true, if $string is null or equals ""
 */
function isEmpty($string){
	return $string==null || $string=="";
}
/**
 * adds $days days to the current date
 * 
 * @param int $days
 * @return a Date-String DD.MM.YYYY
 */
function getDateInDays($days){
	return date("d.m.Y", strtotime("+".$days." days"));
}
?>