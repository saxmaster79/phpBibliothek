<?php
function sendHeaders(){
	header('Content-type: text/html; charset=UTF-8') ;
	header('Expires: Thu, 01 Dec 1994 16:00:00 GMT');
	header( 'Pragma: no-cache' );
}
function htmlheadout($tag){

	//gibt einen html-header inkl. <body> aus mit dem titel $title und dem
	//zusätzlichen tag $tag. Beide Parameter koennen auch "" sein.
	echo"
<html>
	<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
		<title>".TITLE."</title>
		<link rel=\"stylesheet\" type=\"text/css\" href=\"main.css\">
        <script src=\"functions.js\" type=\"text/javascript\">
		</script>
		$tag
	</head>
	<body>
            ";
}
function closebody(){
	echo"
			<br />
			<br />
			<br />
			<br />
			<p class=\"copyleft\">
			phpBibliothek<br />
			Version 2.0.2<br />
			Copyright (C) 2019 Benno Inderbitzin
			</p>
		</body>
	</html>
   	";
}
?>