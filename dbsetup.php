<?php

require_once 'dbconn.php';

$db = new dbconn();

$sqlTable = "CREATE TABLE IF NOT EXISTS `php-report` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `display_name` VARCHAR(50) NOT NULL,
    `description` VARCHAR(500) NULL,
    `url` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id`)
)
DEFAULT CHARSET=utf8
COLLATE='utf8_general_ci'
ENGINE=InnoDB
COMMENT='Contains report details for php-report.';";

$db->execQuery($sqlTable, 1);

?>