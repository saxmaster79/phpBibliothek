<?php
//Config Version 1.6
if (!defined ("DBHOST")){  //falls nicht schon mal included wurde

	define("DBHOST", "localhost");//Server
	define("DBLOGIN", "db12220455-2");//DB-Loginname
	define("DBPASS", "brggT9_u8");//PW
	define("DBNAME", "db12220455-2");//Name der Datenbank
	define("GOOGLE_API_KEY", "AIzaSyCoLOQNfb2t6BqHvlBgH4xwopOrAO6qpWM");

	define("BASEURL", "https://bibliothek.shs-steinen.ch");
	define("DAYS_TO_BORROW", 28);
	define("DEBUG", FALSE);//normalerweise FALSE, nur fuer Entwicklungszwecke auf TRUE setzen.
	define("TITLE", "Schulbibliothek Sprachheilschule Steinen");//Titel der Applikation
}
?>