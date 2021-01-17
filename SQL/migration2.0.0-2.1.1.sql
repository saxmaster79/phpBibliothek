ALTER TABLE `benutzer` CHANGE `Salz` `Salz` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

UPDATE `dbversion` SET `dbversion` = 4;