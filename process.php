<?
include_once("config.php");
$s = new Game();
if ($_GET['burst'])
{
 echo $s->Bursted($_GET['burst']);
}

?>