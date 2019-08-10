<?PHP
include_once("inc/common.inc.php");
$pageRight = RIGHT_COUNTER;

include_once("inc/nav.inc.php");
include_once("inc/forms.inc.php");
include_once("inc/functions.inc.php");
include_once("inc/dbfunctions.inc.php");
$showButton = $_POST['showButton'] ?? null;
$saveButton = $_POST['saveButton'] ?? null;
$deleteButton = $_POST['deleteButton'] ?? null;
$class = $_POST['class'] ?? null;
$name = $_POST['name'] ?? null;
$pupilId = $_POST['pupilId'] ?? null;

$con = dbconnect();
if ($showButton) {
    if ($name == null || $name == "") {
        $name = $_GET['name'] ?? null;
        if (!$name) {
            errorOut("Bitte einen Namen angeben!");
        }
    } else
        $sql = 'SELECT `id`,`Name`, `Klasse`, `Adresse`, `PLZWohnort`, `Telephon` ' .
            'FROM `alleschueler` ' .
            'WHERE `Name` like "' . $name . '%" LIMIT 0, 30';
    $result = dbquery($con, $sql);

    $menge = mysqli_fetch_array($result);
    $menge = arrayAfterDB($menge, mysqli_num_fields($result));

    list ($pupilId, $name, $class, $address, $plz, $phone) = $menge;
    $checked = "";

} else if ($saveButton == " Speichern ") {
    $class = $_POST['class'];
    $address = $_POST['address'];
    $plz = $_POST['plz'];
    $phone = $_POST['phone'];
    $checked = $_POST['checked'];

    $errors = "";
    $errors = checkAttribute($name, $errors, "Name");
    $errors = checkAttribute($class, $errors, "Klasse");

    if ($errors != "") {
        errorOut("Folgende Felder müssen ausgefüllt werden: " . $errors);
    } else {

        $sql = 'SELECT `id`' .
            'FROM `alleschueler` ' .
            'WHERE `Name`="' . $name . '" LIMIT 0, 30';

        $result = dbquery($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            $id = mysqli_fetch_row($result);
            $id = $id[0];
            if ($checked != $id) {
                //ask user if update
                warningOut("Es existiert bereits ein Schüler mit dem Namen " . $name . "! Zum Überschreiben " .
                    "&quot;Speichern&quot; drücken, sonst einen anderen Namen eingeben.");
                $checked = $id;
                $pupilId = $id;
                $class = afterDB($class);
                $address = afterDB($address);
                $plz = afterDB($plz);
                $phone = afterDB($phone);

            } else {

                //update db
                $class = beforeDB($con, $class);
                $address = beforeDB($con, $address);
                $plz = beforeDB($con, $plz);
                $phone = beforeDB($con, $phone);
                $sql = 'UPDATE `alleschueler` SET `Klasse`="' . $class . '", `Adresse`="' . $address . '", ' .
                    '`PLZWohnort`="' . $plz . '", `Telephon`="' . $phone . '" ' .
                    'WHERE `id`="' . $pupilId . '"';
                dbquery($con, $sql);
                if (dberror()) {
                    die();
                } else {
                    $checked = "";
                    $class = afterDB($class);
                    $address = afterDB($address);
                    $plz = afterDB($plz);
                    $phone = afterDB($phone);

                    statusOut("Daten wurden aktualisiert");
                }
            }
        } else {
            //save to db
            $class = beforeDB($con, $class);
            $address = beforeDB($con, $address);
            $plz = beforeDB($con, $plz);
            $phone = beforeDB($con, $phone);

            $sql = 'INSERT INTO `alleschueler` (`Name`, `Klasse`, `Adresse`, `PLZWohnort`, `Telephon`) ' .
                'VALUES ("' . $name . '", "' . $class . '", "' . $address . '", "' . $plz . '", "' . $phone . '")';
            dbquery($con, $sql);
            if (dberror()) {
                die();
            } else {
                statusOut("Daten wurden gespeichert");
                $pupilId = "";
                $name = "";
                $class = "";
                $address = "";
                $plz = "";
                $phone = "";
            }
        }
    }

} elseif ($deleteButton == " Löschen ") {
    //Überprüfen ob der Schüler noch ausgeliehene Bücher hat
    dbconnect();
    $sql = 'SELECT id FROM `ausleihen` WHERE schuelerId="' . $pupilId . '" AND zurueckgebracht="0" LIMIT 0, 30';
    $noOfBooks = mysqli_num_rows(dbquery($con, $sql));
    if ($noOfBooks > 0) {
        errorOut("Der Schüler hat noch $noOfBooks Bücher ausgeliehen! Er kann deshalb nicht gelöscht werden.");
    } else {
        //display yes/no-dialog
        $title = "Schüler löschen bestätigen";
        $text = "Soll der Schüler $name wirklich gelöscht werden?";
        require_once("dialog.inc.php");
        yesOrNoDialog($title, $text, "pupilId", $pupilId, "delete.php");
    }
} else {
    //Standardwerte für neuen Schüler:
    $pupilId = "";
    $checked = "";
}

?>
<h1>Schüler eingeben</h1>
<?php
dbconnect();
$sql = 'SELECT `Klasse`,`Klasse` from `klassen`';
$classResult = dbquery($con, $sql);

openForm("enterpupil.php");
hiddenField("pupilId", $pupilId);
hiddenField("checked", $checked);
textFieldSubmitRow("Name Vorname", "name", $name, "Anzeigen", "showButton");
selectionRow("Klasse", "class", $classResult, $class, $con);
//textFieldRow("Adresse", "address", $address);
//textFieldRow("PLZ Wohnort", "plz", $plz);
//textFieldRow("Telefon", "phone", $phone);
if ($pupilId == "") {
    twoSubmitTableRowOneDisabled("Löschen", "Speichern", "deleteButton", "saveButton", 1);
} else {
    twoSubmitTableRow("Löschen", "Speichern", "deleteButton", "saveButton");
}
closeForm();
closebody();

?>

