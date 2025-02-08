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
You made it to the fleet docks.
<?php
echo "Query Count: " . $s->queryCount . "<br>";
$pagegen->stop();
print('page generation time: ' . $pagegen->gen());
?>