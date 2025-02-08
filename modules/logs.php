<?php
include("../config.php");

$pagegen = new page_gen();
$pagegen->round_to = 4;
$pagegen->start();

$s = new Game();
if (!$s->loggedIn || !$_GET['time']) {
    header("Location: https://realmbattles.org/SGWnew/index.php?");
    exit;
}
$s->updatePower($_SESSION['userid']);
?>
<center>
    <a href="javascript:void(0)" onclick="sendData('logs','get','id','attack');return false">Attack</a> | 
    <a href="javascript:void(0)" onclick="sendData('logs','get','id','raid');return false">Raid</a> | 
    <a href="javascript:void(0)" onclick="sendData('logs','get','id','spy');return false">Spy</a> | 
    <a href="javascript:void(0)" onclick="sendData('logs','get','id','sab');return false">Sabotage</a>
</center>
<?php
$s->actionLog($_GET['atype']);

echo "Query Count: " . $s->queryCount . "<br>";
$pagegen->stop();
print('page generation time: ' . $pagegen->gen());
?>