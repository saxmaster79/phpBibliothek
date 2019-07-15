<?php
/**
 * Sucht alle verschiedenen Gruppen aus der Datenbank.
 * dbconnect() muss vorher aufgerufen werden
 * @return result
 */
function getGroupsFromDB($connection){
	$sql= private_bastel_sql('Gruppe');
	return dbQuery($connection, $sql);
}
/**
 * Sucht alle verschiedenen Standorte aus der Datenbank.
 * dbconnect() muss vorher aufgerufen werden
 * @return result
 */
function getLocationsFromDB($connection){
	$sql= private_bastel_sql('Standort');
	return dbQuery($connection, $sql);
}
/**
 * Sucht alle verschiedenen Medien aus der Datenbank.
 * dbconnect() muss vorher aufgerufen werden
 * @return result
 */
function getMediaFromDB($connection){
	$sql= private_bastel_sql('Medium');
	return dbQuery($connection, $sql);
}

function private_bastel_sql($colName){
	return 'SELECT DISTINCT `'.$colName.'`, `'.$colName.'` FROM `allebuecher` where `'.$colName.'` is not null AND `'.$colName.'` <>"" ORDER BY `'.$colName.'` LIMIT 0, 300';
}
?>