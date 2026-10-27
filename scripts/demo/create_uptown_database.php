<?php
/**
 * Create the configured local Dolibarr database if it does not exist.
 */

define('DOL_DOCUMENT_ROOT', realpath(__DIR__.'/../../htdocs'));
$conf = new stdClass();
$conf->db = new stdClass();
$conf->db->character_set = 'utf8mb4';
$conf->db->dolibarr_main_db_collation = 'utf8mb4_unicode_ci';
$langs = new stdClass();
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once __DIR__.'/../../htdocs/core/db/mysqli.class.php';

$databaseName = 'uptown_global_solutions';
$database = new DoliDBMysqli('mysqli', '127.0.0.1', 'root', '', '', 3306);
if (!$database->connected) {
	fwrite(STDERR, "Unable to connect to MySQL: ".$database->lasterror()."\n");
	exit(1);
}

$sqlCreateDatabase = "CREATE DATABASE IF NOT EXISTS `".$database->escape($databaseName)."` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (!$database->query($sqlCreateDatabase)) {
	fwrite(STDERR, "Unable to create database: ".$database->lasterror()."\n");
	exit(1);
}

print "Database ".$databaseName." is ready.\n";
