<?php
include("../config.php");

$pagegen = new page_gen();
$pagegen->round_to = 4;
$pagegen->start();

$s = new Game();
if (!$s->loggedIn || !$_GET['time']){ header("Location: https://realmbattles.org/SGWnew/index.php?"); }
$s->updatePower($_SESSION['userid']);
if ($_GET['page']) 
{
	$rankings = $s->allyRankings($_GET['page'],$_GET['id']);
	$allyinfo = $s->getallyinfo($_GET['id']);
}
else
{
	$rankings = $s->allyRankings('1',$_GET['id']);
	$allyinfo = $s->getallyinfo($_GET['id']);
}
?>
<table width="100%" border="0">
  <tr>
    <td>Name</td>
    <td>Rank</td>
    <td>Army Size </td>
    <td>Race</td>
    <td>Treasury</td>
  </tr>
<?
for($x = 0; $x < count($rankings); $x++)
{
  if(!$rankings[$x]['rank'] == 0){?>
    <tr>
  	  <td><a href='javascript:void(0)' onclick="sendData('user','get','<?= $rankings[$x]['uid']; ?>')"><?= $rankings[$x]['name']; ?></a>[<?= $allyinfo->allyname;?>]</a></td>
    	<td><?= $rankings[$x]['rank']; ?></td>
    	<td><?= $rankings[$x]['army']; ?></td>
    	<td><?= $rankings[$x]['race']; ?></td>
    	<td><?= $rankings[$x]['cash']; ?></td>
  		</tr>
	
<?
}
}
?>
</table>
<?
echo "Query Count: ".$s->queryCount."<br>";
$pagegen->stop();
print('page generation time: ' . $pagegen->gen());
?>