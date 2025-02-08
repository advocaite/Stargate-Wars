<?php
include_once("../config.php");

$pagegen = new page_gen();
$pagegen->round_to = 4;
$pagegen->start();

$s = new Game();
if (!$s->loggedIn || !$_GET['time']){ header("Location: index.php?"); }
if (!$_POST) { $s->updatePower($_SESSION['userid']); }
$weapons = $s->getWeapons($_SESSION['userid']);
if($_REQUEST['atype'] == "repair")
{
	$id = $_REQUEST['id'];
	$query = "UPDATE `weapons` SET `strength`=(SELECT weaponPower FROM armory WHERE wid =$id) WHERE uid=".$_SESSION['userid']." AND wid=$id";
	$s->query($query);
	echo "Weapon Repaired";
}

if($_POST['submit']!="Submit")
{
	$posted = array();
	for ($x = 0; $x < count($weapons['atk']); $x++)
	{
		$posted[$weapons['atk'][$x]['fieldname']] = $_POST[$weapons['atk'][$x]['fieldname']];
	}
	for ($x = 0; $x < count($weapons['def']); $x++)
	{
		$posted[$weapons['def'][$x]['fieldname']] = $_POST[$weapons['def'][$x]['fieldname']];
	}
	$s->buyWeapons($posted);
	$s->updatePower($_SESSION['userid']);
}
$inv = $s->getWeaponInventory($_SESSION['userid']);
?>
<table width="100%" border="0">
  <tr>
    <td colspan="2"><table width="100%" border="0">
      <tr>
        <td colspan="5" align="center" valign="middle">Current Weapon Inventory </td>
        </tr>
      <tr>
        <td width="22%" align="left" valign="middle">Attack Weapons</td>
        <td width="27%" align="center" valign="middle">Quanity</td>
        <td width="16%" align="center" valign="middle">Strength</td>
        <td width="13%" align="center" valign="middle">Repair</td>
        <td width="22%" align="center" valign="middle">Scrap /Sell </td>
      </tr>
      <? for ($x = 0; $x < count($inv['atk']); $x++){?>
	  <tr>
        <td align="left" valign="middle"><?= $inv['atk'][$x]['name'];?></td>
        <td align="center" valign="middle"><?= $inv['atk'][$x]['quanity'];?></td>
		<td align="center" valign="middle"><?= $inv['atk'][$x]['strength']."/".$inv['atk'][$x]['power'];?></td>
        <td align="center" valign="middle"><a href="javascript:void(0)" onclick="sendData('armory','get','<?= $inv['atk'][$x]['wid'];?>','repair'); return false;"><?= $inv['atk'][$x]['perpoint'];?></a></td>
        <td align="center" valign="middle"><input name="<?= $inv['atk'][$x]['fieldname'];?>" type="text" value="<?= $inv['atk'][$x]['quanity'];?>" size="10" /> for <?= $inv['atk'][$x]['sell'];?> each</td>
      </tr>
	  <?php } ?>
	  <tr><td>&nbsp;</td></tr>
      <tr>
        <td align="left" valign="middle">Defense Weapons</td>
        <td align="center" valign="middle">Quanity</td>
        <td align="center" valign="middle">Strength</td>
        <td align="center" valign="middle">Repair</td>
        <td align="center" valign="middle">Scrap /Sell </td>
      </tr>
      <?php for ($x = 0; $x < count($inv['def']); $x++){?>
	  <tr>
        <td align="left" valign="middle"><?= $inv['def'][$x]['name'];?></td>
        <td align="center" valign="middle"><?= $inv['def'][$x]['quanity'];?></td>
		<td align="center" valign="middle"><?= $inv['def'][$x]['strength']."/".$inv['def'][$x]['power']; ?></td>
        <td align="center" valign="middle"><a href="javascript:void(0)" onclick="sendData('armory','get','<?= $inv['def'][$x]['wid'];?>','repair'); return false;"><?= $inv['def'][$x]['perpoint'];?></a></td>
        <td align="center" valign="middle"><input name="<?= $inv['def'][$x]['fieldname'];?>" type="text" value="<?= $inv['def'][$x]['quanity'];?>" size="10" /> for <?= $inv['def'][$x]['sell'];?> each</td>
      </tr>
	  <? } ?>
	  <tr><td>&nbsp;</td></tr>
    </table></td>
  </tr>
  <tr>
    <td width="37%" align="left" valign="top"><? include_once('mil_rank.php'); echo "<br>"; include_once('personnel.php'); ?>
    <br /></td>
    <td width="63%" align="right" valign="top"><form action="javascript:void(0)"><table width="90%" border="0">
      <tr>
        <td colspan="4" align="center" valign="top"> Weapons </td>
        </tr>
      <tr>
        <td width="26%" align="left" valign="top">Attack Weapons </td>
        <td width="18%" align="right">Power</td>
        <td width="40%" align="right">Cost</td>
        <td width="16%" align="right">Quanity</td>
      </tr>
          <?php
	  for ($x = 0; $x < count($weapons['atk']); $x++)
	  {
	  	if($weapons['atk'][$x]['unitcost'] ==0 && !$weapons['atk'][$x]['cashcost'] ==0)
		{
	    ?>
	  <tr>
        <td width="26%" align="left" valign="top"><?= $weapons['atk'][$x]['name'];?></td>
        <td width="18%" align="right" valign="top"><?= $weapons['atk'][$x]['power'];?></td>
        <td width="40%" align="right" valign="top"><?= $weapons['atk'][$x]['cashcost'];?> naquadrea</td>
        <td width="16%" align="right" valign="bottom"><input name="<?= $weapons['atk'][$x]['fieldname'];?>" type="text" value="0" size="10" /></td>
      </tr>
        <?php
	  	}elseif ($weapons['atk'][$x]['cashcost'] ==0 && !$weapons['atk'][$x]['unitcost'] ==0 ){
	    ?>
	  <tr>
        <td width="26%" align="left" valign="top"><?= $weapons['atk'][$x]['name'];?></td>
        <td width="18%" align="right" valign="top"><?= $weapons['atk'][$x]['power'];?></td>
        <td width="40%" align="right" valign="top"><?= $weapons['atk'][$x]['unitcost'];?> untrained units</td>
        <td width="16%" align="right" valign="bottom"><input name="<?= $weapons['atk'][$x]['fieldname'];?>" type="text" value="0" size="10" /></td>
      </tr>
	  	<?php
		}else if(!$weapons['atk'][$x]['cashcost'] ==0 && (!$weapons['atk'][$x]['unitcost'] ==0)){		
		?>
	  <tr>
        <td width="26%" align="left" valign="top"><?= $weapons['atk'][$x]['name'];?></td>
        <td width="18%" align="right" valign="top"><?= $weapons['atk'][$x]['power'];?></td>
        <td width="40%" align="right" valign="top"><?= $weapons['atk'][$x]['cashcost'];?> naquadrea and<br /> 
          <?= $weapons['atk'][$x]['unitcost'];?> untrained units</td>
        <td width="16%" align="right" valign="bottom"><input name="<?= $weapons['atk'][$x]['fieldname'];?>" type="text" value="0" size="10" /></td>
      </tr>
	  <?php
	  }
	 }
	  ?><tr><td>&nbsp;</td></tr>
	  <tr>
        <td width="26%" align="left" valign="top">Defense Weapons </td>
        <td width="18%" align="right">Power</td>
        <td width="40%" align="right">Cost</td>
        <td width="16%" align="right" valign="bottom">Quanity</td>
      </tr>
          <?php
	  for ($x = 0; $x < count($weapons['def']); $x++)
	  {
	  	if($weapons['def'][$x]['unitcost'] ==0  && !$weapons['def'][$x]['cashcost'] ==0 )
		{
	    ?>
	  <tr>
        <td width="26%" align="left" valign="top"><?= $weapons['def'][$x]['name'];?></td>
        <td width="18%" align="right" valign="top"><?= $weapons['def'][$x]['power'];?></td>
        <td width="40%" align="right" valign="top"><?= $weapons['def'][$x]['cashcost'];?> naquadrea</td>
        <td width="16%" align="right" valign="bottom"><input name="<?= $weapons['def'][$x]['fieldname'];?>" type="text" value="0" size="10" /></td>
      </tr>
        <?php
	  	}elseif ($weapons['def'][$x]['cashcost'] ==0 && !$weapons['def'][$x]['unitcost'] ==0  ){
	    ?>
	  <tr>
        <td width="26%" align="left" valign="top"><?= $weapons['def'][$x]['name'];?></td>
        <td width="18%" align="right" valign="top"><?= $weapons['def'][$x]['power'];?></td>
        <td width="40%" align="right" valign="top"><?= $weapons['def'][$x]['unitcost'];?> untrained units</td>
        <td width="16%" align="right" valign="bottom"><input name="<?= $weapons['def'][$x]['fieldname'];?>" type="text" value="0" size="10" /></td>
      </tr>
	  	<?php
		}else if(!$weapons['def'][$x]['cashcost'] ==0 && (!$weapons['def'][$x]['unitcost'] ==0)){		
		?>
	  <tr>
        <td width="26%" align="left" valign="top"><?= $weapons['def'][$x]['name'];?></td>
        <td width="18%" align="right" valign="top"><?= $weapons['def'][$x]['power'];?></td>
        <td width="40%" align="right" valign="top"><?= $weapons['def'][$x]['cashcost'];?> naquadrea and <br />
          <?= $weapons['def'][$x]['unitcost'];?> untrained units</td>
        <td width="16%" align="right" valign="bottom"><input name="<?= $weapons['def'][$x]['fieldname'];?>" type="text" value="0" size="10" /></td>
      </tr>
	  <?php
	  }
	 }
	  ?>
	  <tr><td>&nbsp;</td></tr>
      <tr>
        <td colspan="4" align="right" valign="bottom"><input type="submit" name="buyweaps" value="Submit" onclick="this.value='Buying Weapons...'; this.disabled=true; sendData('armory','post','<?= $s->uid;?>')"/></td>
        </tr>
    </table

></table>
<?php
echo "Query Count: ".$s->queryCount."<br>";
$pagegen->stop();
print('page generation time: ' . $pagegen->gen());
?>