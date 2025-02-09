<?php
include_once("config.php");
$s = new Game();
if($s->turnUpdate())
{
	echo "Successful<br>";
	echo $s->queryCount;
	
}
?>