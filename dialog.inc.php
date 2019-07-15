<?php
include_once("inc/forms.inc.php");
function yesOrNoDialog($title, $text, $name, $value, $action){
	echo"
			<div class='warningField'>
				<h2>$title</h2>
				<p>$text</p>
	";
	openForm($action);
	hiddenField($name, $value);
	openTableRow();
	submitButton("Ja", "yesButton");
	backButton("Nein");
	closeTableRow();
	closeForm();
	?>
	 		</div>
	 	</body>
	 </html>
	<?php
	die();	
}