<?php

//	ISBN Functions
//	Compiled by Adam Reineke (Jan 12, 2007)
//		from original code and comments posted on
//		http://www.blyberg.net/2006/04/05/php-port-of-isbn-1013-tool/
 
function isbn_convert($isbn) {
		$isbn2 = substr("978" . trim($isbn), 0, -1);
		$sum13 = isbn_genchksum13($isbn2);
		$isbn13 = "$isbn2-$sum13";
		return ($isbn13);
	}
 
function isbn_gettype($isbn) {
		$isbn = trim($isbn);
		if (preg_match('%[0-9]{12}?[0-9Xx]%s', $isbn)) {
			if (isbn_validatettn($isbn)==1){
					return 13;
					   } else {return(-1);}
 
				} else if (preg_match('%[0-9]{9}?[0-9Xx]%s', $isbn)) {
			  if (isbn_validateten($isbn)==1){
					return 10;
					   } else {return -1;}
		} else {
			return -1;
		}
	}
 
function isbn_validateten($isbn) {
		$isbn = trim($isbn);
		$chksum = substr($isbn, -1, 1);
		$isbn = substr($isbn, 0, -1);
		if (preg_match('/X/i', $chksum)) { $chksum="10"; }
		$sum = isbn_genchksum10($isbn);
		if ($chksum == $sum){
			return 1;
		}else{
			return 0;
		}
	}
 
function isbn_validatettn($isbn) {
		$isbn = trim($isbn);
		$chksum = substr($isbn, -1, 1);
		$isbn = substr($isbn, 0, -1);
		if (preg_match('/X/i', $chksum)) { $chksum="10"; }
		$sum = isbn_genchksum13($isbn);
		if ($chksum == $sum){
			return 1;
		}else{
			return 0;
		}
	}
 
function isbn_genchksum13($isbn) {
	$t = 2;
	$isbn = trim($isbn);
	$b=0;
	for($i = 1; $i <= 12; $i++){
		$c = substr($isbn,($i-1),1);
		if ($i % 2==0){
			$a = (3 * $c);
		} else {
			$a = (1 * $c);
		}
	$b=$b+$a;
	}
	$sum = 10 - ($b % 10);
	if($sum == 10) $sum = 0;
	return $sum;
	}
 
function isbn_genchksum10($isbn) {
	$t = 2;
	$isbn = trim($isbn);
	$b=0;
	for($i = 0; $i < 9; $i++){
	   $c = substr($isbn,$i,1);
	   $a = (($i+1) * $c);
	   $b=$b+$a;
	}
	$sum = ($b % 11);
	return $sum;
	}

//Clean out the dashes and spaces from passed ISBN
function isbn_cleandashes($isbn){
	return $isbn = str_replace(array('-', ' '), '', $isbn);
	}

//Convert ISBN-13 back into ISBN-10
function convert13($isbn) {
	$isbn2 = substr("" . trim($isbn), 3, 9);
	$sum10 = $this->genchksum10($isbn2);
	if ($sum10==10) {$sum10='X';}
	$isbn10 = $isbn2.$sum10;
	return ($isbn10);
	}
		
/** ISBN Add Dashes*/
function isbn_dashes($isbn) {
	switch(strlen($isbn)):
		case 13:
			return
				substr($isbn,0,3)."-"
				.substr($isbn,3,1)."-"
				.substr($isbn,4,3)."-"
				.substr($isbn,7,5)."-"
				.substr($isbn,12,1);
			break;
		case 10:
			return
				substr($isbn,0,1)."-"
				.substr($isbn,1,3)."-"
				.substr($isbn,4,5)."-"
				.substr($isbn,9,1);
			break;
		default: return false; break;
	endswitch;
	}


?>
