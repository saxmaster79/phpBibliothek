ALTER TABLE `allebuecher`
    ADD `Neupreis` DECIMAL NULL DEFAULT NULL AFTER `Standort`,
    ADD `Beschaffung` DATE NULL DEFAULT NULL AFTER `Neupreis`,
    ADD `Zählung` INT NULL DEFAULT NULL AFTER `Reihe`;
ALTER TABLE `klassen` CHANGE `Klasse` `Klasse` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `alleschueler` CHANGE `Klasse` `Klasse` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
UPDATE `dbversion` SET `dbversion` = 3;