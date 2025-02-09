<?php
// Config File
session_cache_expire(180);
session_start();

// General Information
$subs['{TITLE}'] = "Pegus Galaxy";						# Name of site(header)
$subs['{SUBTITLE}'] = "The place to get your war on!";	# Second header(subhead)
$subs['{ADMIN_EMAIL}'] = "mcrodneyau@gmail.com";			# Person to email if something goes wrong
$subs['{HEAD_STUFF}'] = "";								# Stuff to put in <head>(left blank intentionally)

// Database Information
$conf['db_server'] = "localhost";						# IP or hostname of DB server(usually localhost)
$conf['db_name']  = "sgw";						# Name of DB within the server
$conf['db_username']  = "root";							# Username for DB
$conf['db_password']  = "";						# Password for DB
$conf['db_prefix'] = "";							# Prefix for DB tables
// Set Error Reporting
//error_reporting(E_ALL | E_STRICT);

define("PATH", dirname(__FILE__));
define("SCRIPT_PATH",PATH."/base/");
define("TEMPLATES_PATH",PATH."/templates/");
define("DEBUG",false);

include(SCRIPT_PATH."Chive.class.php");
include(SCRIPT_PATH."User.class.php");
include(SCRIPT_PATH."Debug.class.php");
include(SCRIPT_PATH."functions.php");
include(SCRIPT_PATH."Game.class.php");
?>
