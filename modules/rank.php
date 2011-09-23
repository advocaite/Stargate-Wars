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
	$rankings = $s->Rankings($_GET['page']);

}
else
{
	$rankings = $s->Rankings('1');

}
?>
<table width="100%" border="0">
  <tr>
    <td>Name</td>
    <td>Rank</td>
    <td>Army Size </td>
    <td>Race</td>
    <td>Treasury</td>
	<td>Attack</td>
  </tr>
<?
for($x = 0; $x < count($rankings); $x++)
{
  if(!$rankings[$x]['rank'] == 0){
  $allyinfo = $s->getallyinfo($rankings[$x]['allyid']);?>
    <tr>
	
  	  <td><a href='javascript:void(0)' onclick="sendData('user','get','<?= $rankings[$x]['uid']; ?>')"><?= $rankings[$x]['name']; ?></a><?if ($rankings[$x]['allyid'] != 0){ ?> [<a href="javascript:void(0)" onclick="sendData('ally_mlist','get','<?= $rankings[$x]['allyid']; ?>','attack'); return false;"><?= $allyinfo->allyname;?></a>]<?}else{}?></td>
    	<td><?= $rankings[$x]['rank']; ?></td>
    	<td><?= $rankings[$x]['army']; ?></td>
    	<td><?= $rankings[$x]['race']; ?></td>
    	<td><?= $rankings[$x]['cash']; ?></td>
		<?if ($rankings[$x]['uid'] != $_SESSION[userid]){?>
		<td><a href="javascript:void(0)" onclick="sendData('action','get','<?= $rankings[$x]['uid']; ?>','attack'); return false;">Attack</a></td><?}else{?>
       <td></td><?}?>
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