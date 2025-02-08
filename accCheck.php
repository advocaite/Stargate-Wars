<?php
include("config.php");
$s = new User();
$query = "
	SELECT aname, alevel 
	FROM ".$s->db_prefix."access
	";
$q = $s->query($query);
showPage();
$q->free();
?>