<?php
include_once("../config.php");

$pagegen = new page_gen();
$pagegen->round_to = 4;
$pagegen->start();

$s = new Game();
if($_GET['atype'] == "attack" || $_GET['atype'] == "raid")
{
	$type = $_GET['atype'];
	$touid = $_GET['id'];
	$time = $s->attack_raid($type,$touid,'15');
	$host  = $_SERVER['HTTP_HOST'];
	$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	header("Location: http://$host$uri/actionLogs.php?id=".$time."&time=".microtime());
}

if($_GET['atype'] == "spy")
{
	$touid = $_GET['id'];
	$time = $s->spy($touid,'1');
	$host  = $_SERVER['HTTP_HOST'];
	$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	header("Location: http://$host$uri/actionLogs.php?id=".$time."&time=".microtime());
}
exit;
?>