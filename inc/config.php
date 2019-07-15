<?php
//Config Version 1.6
if (!defined ("DBHOST")){  //falls nicht schon mal included wurde

/*
	define("DBHOST", "localhost");//Server
	define("DBLOGIN", "web111");//DB-Loginname
	define("DBPASS", "ste15we2");//PW
	define("DBNAME", "usr_web111_2");//Name der Datenbank
*/

      // 07.10.14, Anpassung durch webFormat (Anpassung an Hosting-Änderung)
	define("DBHOST", "localhost");//Server
	define("DBLOGIN", "db12220455-2");//DB-Loginname
	define("DBPASS", "brggT9_u8");//PW
	define("DBNAME", "db12220455-2");//Name der Datenbank

	define("BASEURL", "http://www.shs-steinen.ch/bibliothek/");
	define("DAYS_TO_BORROW", 28);
	define("DEBUG", TRUE);//normalerweise FALSE, nur fuer Entwicklungszwecke auf TRUE setzen.
	define("TITLE", "Schulbibliothek Sprachheilschule Steinen");//Titel der Applikation
}
?>