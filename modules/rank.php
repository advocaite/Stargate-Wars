<?php
include("../config.php");

$pagegen = new page_gen();
$pagegen->round_to = 4;
$pagegen->start();

$s = new Game();
if (!$s->loggedIn || !$_GET['time']){ header("Location: https://realmbattles.org/SGWnew/index.php?"); }
$s->updatePower($_SESSION['userid']);

$page = $_GET['page'] ?? '1';
$rankings = $s->Rankings($page);

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
<?php
for($x = 0; $x < count($rankings); $x++)
{
  if($rankings[$x]['rank'] != 0){
  $allyinfo = $s->getallyinfo($rankings[$x]['allyid']); ?>
    <tr>
	
  	  <td><a href='javascript:void(0)' onclick="sendData('user','get','<?= htmlspecialchars($rankings[$x]['uid'], ENT_QUOTES, 'UTF-8'); ?>')"><?= htmlspecialchars($rankings[$x]['name'], ENT_QUOTES, 'UTF-8'); ?></a><?php if ($rankings[$x]['allyid'] != 0){ ?> [<a href="javascript:void(0)" onclick="sendData('ally_mlist','get','<?= htmlspecialchars($rankings[$x]['allyid'], ENT_QUOTES, 'UTF-8'); ?>','attack'); return false;"><?= htmlspecialchars($allyinfo->allyname, ENT_QUOTES, 'UTF-8');?></a>]<?php } ?></td>
    	<td><?= htmlspecialchars($rankings[$x]['rank'], ENT_QUOTES, 'UTF-8'); ?></td>
    	<td><?= htmlspecialchars($rankings[$x]['army'], ENT_QUOTES, 'UTF-8'); ?></td>
    	<td><?= htmlspecialchars($rankings[$x]['race'], ENT_QUOTES, 'UTF-8'); ?></td>
    	<td><?= htmlspecialchars($rankings[$x]['cash'], ENT_QUOTES, 'UTF-8'); ?></td>
		<?php if ($rankings[$x]['uid'] != $_SESSION['userid']){ ?>
		<td><a href="javascript:void(0)" onclick="sendData('action','get','<?= htmlspecialchars($rankings[$x]['uid'], ENT_QUOTES, 'UTF-8'); ?>','attack'); return false;">Attack</a></td><?php } else { ?>
       <td></td><?php } ?>
  		</tr>
	
<?php
  }
}
?>
</table>
<?php
echo "Query Count: " . $s->queryCount . "<br>";
$pagegen->stop();
print('page generation time: ' . $pagegen->gen());
?>