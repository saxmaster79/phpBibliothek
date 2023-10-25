<?php
//Config Version 1.6
if (!defined ("DBHOST")){  //falls nicht schon mal included wurde

    define("DBHOST", "localhost");//Server
    define("DBLOGIN", "login");//DB-Loginname
    define("DBPASS", "pwd");//PW
    define("DBNAME", "dbname");//Name der Datenbank

    define("BASEURL", "");
    define("DAYS_TO_BORROW", 28);
    define("DEBUG", TRUE);//normalerweise FALSE, nur fuer Entwicklungszwecke auf TRUE setzen.
    define("TITLE", "Hier kommt der titel");//Titel der Applikation
}
?>