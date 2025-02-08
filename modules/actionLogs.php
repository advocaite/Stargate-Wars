<?php
include_once("../config.php");

$pagegen = new page_gen();
$pagegen->round_to = 4;
$pagegen->start();

$s = new Game();
if (!$s->loggedIn){ header("Location:  https://realmbattles.org/SGWnew/index.php?"); }
$s->updatePower($_SESSION['userid']);
if ($_GET['id'])
{
		if(!$s->getActID($_GET['id'])){	echo "Sorry the Action ID you entered Does not Exist<br>"; }	
}
echo "Query Count: ".$s->queryCount."<br>";
$pagegen->stop();
print('page generation time: ' . $pagegen->gen());
?>