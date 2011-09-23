<?php
include("../config.php");

$pagegen = new page_gen();
$pagegen->round_to = 4;
$pagegen->start();

$s = new Game();
if (!$s->loggedIn || !$_GET['time']){ header("Location: https://realmbattles.org/SGWnew/index.php?"); }
$s->updatePower($_SESSION['userid']);

if ($_GET['id'])
{
 	$uid = $_GET['id'];
}
else
{
	$uid = $s->uid;
}
$user = $s->getUserInfo($uid);
?>
<table width="100%" border="0">
  <tr>
    <td width="56%"><table width="100%" border="0">
 	  <tr align="left" valign="top">
	    <td>User ID</td>
		<td><?= $uid; ?></td>
	  </tr>
      <tr align="left" valign="top">
        <td width="30%">Name</td>
        <td width="70%"><?= $user->userName;  ?></td>
      </tr>

      <tr align="left" valign="top">
        <td>Commander</td>
        <td><? if ($user->cmdrName == "None") { echo "None"; } else {?> <a href="javascript:void(0)" onclick="sendData('user','get','<?= $user->cmdrID; ?>'); return false"><?= $user->cmdrName ; }?></td>
      </tr>
      <tr align="left" valign="top">
        <td>Race</td>
        <td><?= $user->race; ?></td>
      </tr>
      <tr align="left" valign="top">
        <td>Rank</td>
        <td><?= $user->rank; ?></td>
      </tr>
      <tr align="left" valign="top">
        <td>Army Size </td>
        <td><?= $user->armySize;?></td>
      </tr>
      <tr align="left" valign="top">
        <td>Treasury</td>
        <td><?= $user->onHand;?></td>
      </tr>
      <tr align="left" valign="top">
        <td>Relation</td>
        <td>&nbsp;</td>
      </tr>
    </table></td>
    <td width="44%" rowspan="5" align="center" valign="top"><table width="100%" border="0">
      <tr>
        <td colspan="3">Planets</td>
        </tr>
      <tr>
        <td>Name</td>
        <td>Size</td>
        <td>Bonus</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table>
      <table width="100%" border="0">
        <tr>
          <td colspan="3">Officers</td>
        </tr>
        <tr>
          <td>Name</td>
          <td>Race</td>
          <td>Rank</td>
        </tr>
          <?
  $offi = $s->getOfficers($uid);
  for($x=0; $x<count($offi); $x++)
  {
  	echo "<tr><td><a href=\"javascript:void(0)\" onclick=\"sendData('user','get','".$offi[$x]["uid"]."'); return false\">".$offi[$x]["name"]."</a> </td><td>" .$offi[$x]["race"]."</td><td>".$offi[$x]["rank"]."</td></tr>";
  }
    echo "<tr> <td colspan='3'>Number of Officers: ".count($offi)."</td> </tr>"
  ?>


      </table></td>
  </tr>
  <tr>
    <td height="60"><table width="100%" border="0" align="center">
      <tr>
        <td colspan="3" align="center" valign="top"><strong>Actions</strong></td>
        </tr>
      <tr>
        <td align="center" valign="top"><a href="javascript:void(0)" onclick="sendData('sendmessage','get','<?= $uid; ?>'); return false;">Send Message </a></td>
        <td align="center" valign="top"><a href="javascript:void(0)" onclick="sendData('action','get','<?= $uid; ?>','spy'); return false;">Spy</a></td>
        <td align="center" valign="top">Sabotage</td>
      </tr>
      <tr>
        <td align="center" valign="top"><a href="javascript:void(0)" onclick="sendData('action','get','<?= $uid; ?>','attack'); return false;">Attack</a></td>
        <td align="center" valign="top"><a href="javascript:void(0)" onclick="sendData('action','get','<?= $uid; ?>','raid'); return false;">Raid</a></td>
        <td align="center" valign="top">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3" align="center" valign="top"><strong>Fleet Action</strong></td>
        </tr>
      <tr>
        <td align="center" valign="top">Attack</td>
        <td align="center" valign="top">Spy</td>
        <td align="center" valign="top">Sabotage</td>
      </tr>
      <tr>
        <td align="center" valign="top">Attack</td>
        <td align="center" valign="top">Raid</td>
        <td align="center" valign="top">Conquere Planet </td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0">
      <tr>
        <td colspan="3" align="center" valign="top"><strong>Relations</strong></td>
        </tr>
      <tr>
        <td align="center" valign="top">War</td>
        <td align="center" valign="top">Neutral</td>
        <td align="center" valign="top">Peace</td>
      </tr>
      <tr>
        <td rowspan="2" align="center" valign="top">&nbsp;</td>
        <td align="center" valign="top">Make This My Commander </td>
        <td rowspan="2" align="center" valign="top">&nbsp;</td>
      </tr>
      <tr>
        <td align="center" valign="top">(all relations are 24 hours minium) </td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0">
      <tr>
        <td height="21" colspan="3" align="center" valign="top"><p><strong>Command Relations</strong> <br />
        (affects You and All your officers and all of there officer ...etc) </p>
          </td>
        </tr>
      <tr>
        <td align="center" valign="top">Total War </td>
        <td align="center" valign="top"> Neutral </td>
        <td align="center" valign="top">Total Peace</td>
      </tr>
      <tr>
        <td colspan="3" align="center" valign="middle">&nbsp;</td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0">
      <tr>
        <td align="center" valign="top"><strong>Supporter Status</strong></td>
      </tr>
      <tr>
        <td align="center" valign="top">Send Naq <br />
          Send Turns<br />
          Send Units<br />
<h6>1% of resources transfered will be paid to the broker, such is the cost of giving people stuff. <br /><br />Note  that the function is to GIVE - not lend. If you GIVE resources to  someone, the game administration has NO ability to return them to you.  Place your trust wisely, or risk learning one of the lessons the cosmos  can teach the hard way.</h6></td>
      </tr>
    </table></td>
  </tr>
</table>
<?
echo "Query Count: ".$s->queryCount."<br>";
$pagegen->stop();
print('page generation time: ' . $pagegen->gen());
?>
