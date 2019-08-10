<?php
define("ALL", "all");
function openForm($action){
	echo"
	<table class='formTable'>
		<form action='$action' method='POST' accept-charset='utf-8'>
	";
}

function closeForm(){
	echo"
		</form>
	</table>
	";
}

function openTableRow(){
	echo"
		<tr>
	";
}
function openTableRowWithClass($class){
	echo"
		<tr class='$class'>
	";
}

function openChangeTableRow($i){
	if($i%2!=0){
		openTableRowWithClass("even");
	}else{
		openTableRowWithClass("odd");
	}
}

function closeTableRow(){
	echo"
		</tr>
	";
}

function submitButton($label, $name){
	echo"
		<input type='submit' value=' $label ' name='$name'>
	";
}

function backButton($label){
	echo"
		<input type='button' value=' $label ' onClick='history.back();' />
	";	
}

function cancelButton($label){
	echo"
		<input type='reset' value=' $label '>
   ";
}

function submitTableRow($label, $name){
	openTableRow();
	echo"
			<td class='label'>&nbsp;</td>
			<td class='formField'>\n";
	submitButton($label, $name);
	echo"
			</td>\n";
	closeTableRow();
}

function submitCancelTableRow($label, $name){
	openTableRow();
	echo"
			<td class='label'>&nbsp;</td>
			<td class='formField'>\n";
	submitButton($label, $name);
	backButton("Abbrechen");
	echo"
			</td>\n";
	closeTableRow();
}

function blindTableRow(){
	echo"
	<td class='label'>&nbsp;</td>	
	<td class='formField'>&nbsp;</td>\n";
}

function twoSubmitTableRow($label1, $label2, $name1, $name2){
	blindTableRow();
	openTableRow();
	echo"
			<td class='label'>&nbsp;</td>
			<td class='formField'>\n";
	submitButton($label1, $name1);
	submitButton($label2, $name2);
	echo"
			</td>\n";
	closeTableRow();
}
function twoSubmitTableRowOneDisabled($label1, $label2, $name1, $name2,$which){
	blindTableRow();
	openTableRow();
	echo"
			<td class='label'>&nbsp;</td>
			<td class='formField'>\n";
	if($which==2){
		submitButton($label1, $name1);
		echo"<input type='submit' value=' $label2 ' name='$name2' disabled='disabled' />";
	}else{
		echo"<input type='submit' value=' $label1 ' name='$name1' disabled='disabled' />";
		submitButton($label2, $name2);
	}
	echo"
			</td>\n";
	closeTableRow();
}

function selectionRow($label, $name, $resultSet, $default, $con){
	openTableRow();
	selection($label, $name, $resultSet, $default, "", $con);
	closeTableRow();
}

/**
 * creates a dropdown (html select) for the resultSet
 */
function selection($label, $name, $resultSet, $default, $onChange, $con){
	echo"
			<td class='label'>$label:</td>
			<td class='formField'>
				<select name='$name' onChange='$onChange' class='textField'>\n
					<option value=''>[kein(e) $label ausgewählt]</option>
	";
	while($menge=mysqli_fetch_row($resultSet)){
		list ($value, $optionName) = arrayAfterDB($menge, 2);
		if ($value==$default){
			echo"
        			<option selected='selected' value='$value'>$optionName</option>	
        	";			
		}else{
			echo"
        			<option value='$value'>$optionName</option>	
        	";
		}
	}

	echo"
				</select>
			</td>\n";
}


function selectionArrayRow($label, $name, $array, $default){
	openTableRow();
	echo"
			<td class='label'>$label:</td>
			<td class='formField'>
				<select name='$name' class='textField'>\n
					<option value=''>[kein $label ausgewählt]</option>
	";
	foreach ($array as $value => $optionName){

		if ($value==$default){
			echo"
        			<option selected='selected' value='$value'>$optionName</option>	
        	";			
		}else{
			echo"
        			<option value='$value'>$optionName</option>	
        	";
		}
	}

	echo"
				</select>
			</td>\n";
	closeTableRow();
}
/**
 * Macht eine Auswahlliste
 *
 * @param String $label label
 * @param String $name name des Feldes
 * @param resultSet $resultSet
 * @param boolean $showNoneEntry
 * @param boolean $showAllEntry
 * @param unknown_type $default null, "all" or a value from $resultSet
 */
function selectionAllRow($label, $name, $resultSet, $showNoneEntry, $showAllEntry, $default){
	openTableRow();
	selectionAll($label, $name, $resultSet, $showNoneEntry, $showAllEntry, $default);
	closeTableRow();
}


/**
 * Macht eine Auswahlliste
 *
 * @param String $label label
 * @param String $name name des Feldes
 * @param resultSet $resultSet
 * @param boolean $showNoneEntry
 * @param boolean $showAllEntry
 * @param unknown_type $default null, "all" or a value from $resultSet
 */
function selectionAll($label, $name, $resultSet, $showNoneEntry, $showAllEntry, $default){
	echo"
			<td class='label'>$label:</td>
			<td class='formField'>
				<select name='$name'>\n
	";
	if($showNoneEntry){
		if($default == null){
			echo"
					<option selected='selected' value=''>[kein(e) $label ausgewählt]</option>
	";}else {
			echo"
					<option value=''>[kein(e) $label ausgewählt]</option>
	";
	}}
	while($menge=mysqli_fetch_row($resultSet)){
		list ($value, $optionName) = arrayAfterDB($menge, 2);
		if ($value==$default){
			echo"
        			<option selected='selected' value='$value'>$optionName</option>	
        	";			
		}else{
			echo"
        			<option value='$value'>$optionName</option>	
        	";
		}
	}
	if($showAllEntry){
		if($default == ALL){
			echo"
					<option selected='selected' value='".ALL."'>Alle</option>";
		}else{
			echo"
					<option value='".ALL."'>Alle</option>";
		}
		echo"
				</select>
			</td>\n";
	}
}


	function selectionWithAdderRow($label, $name, $resultSet, $default){
		openTableRow();
		selectionWithAdder($label, $name, $resultSet, $default);
		closeTableRow();
	}

	function selectionWithAdder($label, $name, $resultSet, $default){
		echo"
			<td class='label'>$label:</td>
			<td class='formField'>
				<span id='$name"."0'>
				<select name='$name' id='$name"."selectId' class='textField'>\n
					<option value=''>[kein $label ausgewählt]</option>
	";
		$inserted=false;
		while($menge=mysqli_fetch_row($resultSet)){
			list ($value, $optionName) = arrayAfterDB($menge, 2);
			if ($value==$default){
				echo"
        			<option selected='selected' value='$value'>$optionName</option>	
        	";
				$inserted=true;
			}else{
				echo"
        			<option value='$value'>$optionName</option>	
        	";
			}
		}
		if(!$inserted&&$default!=""&&$default!=null){
			echo"
					<option selected='selected' value='$default'>$default</option>
		";	
		}
		echo"
				</select>
				</span> 
				<input type=\"button\" name=\"Verweis\" value=\"$label hinzufügen\"
				onClick=\"addTextField('$name', this, '$name"."selectId')\">
			</td>\n";
	}

	function textFieldSubmitRow($label, $name, $value, $buttonLabel, $buttonName){
		opentableRow();
		echo"
			<td class='label'>$label:</td>
			<td class='formField'>
				<input type='text' name='$name' value='$value' class='textField' />";
		submitButton($buttonLabel, $buttonName);
		echo"
			</td>	
	";

		closeTableRow();
	}

	function textDisplayRow($label, $toDisplay){
		openTableRow();
		echo"
			<td class='label'>". ($label ? $label : "") ."</td>
			<td class='formField'>
			$toDisplay
			</td>	
	";
			closeTableRow();
	}

	function textFieldRow($label, $name, $value){
		openTableRow();
		echo"
			<td class='label'>$label:</td>
			<td class='formField'>
				<input type='text' name='$name' value='$value' class='textField' />
			</td>	
	";
		closeTableRow();
	}

	function passwordFieldRow($label, $name){
		openTableRow();
		echo"
			<td class='label'>$label:</td>
			<td class='formField'>
				<input type='password' name='$name' value='' class='textField' />
			</td>	
	";
		closeTableRow();
	}

	function textAreaRow($label, $name, $value){
		openTableRow();
		echo"
			<td class='label'>$label:</td>
			<td class='formField'>
				<textarea name='$name' class='textArea'>$value</textarea>
			</td>	
	";
		closeTableRow();
	}
	function hiddenField($name, $value){
		echo"
			<input type='hidden' name='$name' value='$value' />
	";
	}
	function errorOut($msg){
		echo"
		<div class='errorField'>
			<h2>Fehler!</h2>
			$msg
		</div>";
	}

	function warningOut($msg){
		echo"
		<div class='warningField'>
			<h2>Achtung!</h2>
			$msg
		</div>";
	}

	function errorDie($msg){
		echo"
		<div class='errorField'>
			<h2>Fehler!</h2>
			$msg <br  />
			<a href='javascript:history.back()'>zurück</a>		
		</div>";
			die();
	}

	function statusOut($msg){
		echo"
		<div class='statusField'>
		$msg
		</div>";
	}

	?>