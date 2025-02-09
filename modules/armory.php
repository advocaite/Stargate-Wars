<?php
include_once("../config.php");

$pagegen = new page_gen();
$pagegen->round_to = 4;
$pagegen->start();

$s = new Game();
if (!$s->loggedIn || !$_GET['time']){ header("Location: index.php"); }
if (!$_POST) { $s->updatePower($_SESSION['userid']); }
$weapons = $s->getWeapons($_SESSION['userid']);
if($_REQUEST['atype'] == "repair")
{
	$id = $_REQUEST['id'];
	$query = "UPDATE `weapons` SET `strength`=(SELECT weaponPower FROM armory WHERE wid =$id) WHERE uid=".$_SESSION['userid']." AND wid=$id";
	$s->query($query);
	echo "Weapon Repaired";
}

if($_REQUEST['atype'] == "sellweps")
{
	$id = $_REQUEST['id'];
	$wid = $_REQUEST['subject'];
	$query = "SELECT armory.wid, armory.weaponName , weapons.strength, armory.weaponPower, 
		                 armory.cash_cost, armory.isDefense, weapons.quanity
			  	  FROM `armory`,`weapons`,`userdata`
				  WHERE weapons.uid = ".$_SESSION['userid']." AND weapons.wid = ".$wid."
				  AND armory.wid = weapons.wid
				  AND userdata.uid = weapons.uid
				  AND armory.rid = userdata.rid
				  LIMIT 1000";
		$q = mysql_query($query);		 
		$weaps = mysql_fetch_object($q);
		
	$costtosell = $id*($weaps->cash_cost*($weaps->strength/$weaps->weaponPower)) * .80;
	//$query = "UPDATE `weapons` SET `strength`=(SELECT weaponPower FROM armory WHERE wid =$id) WHERE uid=".$_SESSION['userid']." AND wid=$id";
	//$s->query($query);
	//echo $id;
	//echo ",";
	//echo $wid;
	//echo",";
	//echo $costtosell;
	if($id > $weaps->quanity){
	echo "you dont have that many weapons";
	die;
	}else if($id < 0){
	echo "you can not sell negitive weapons, you have been logged";
	die;
	}else{
	$query = "UPDATE `weapons` SET `quanity`= `quanity` - '".$id."' WHERE `uid` =".$_SESSION['userid']." AND `wid`=".$wid." LIMIT 1";
	mysql_query($query);
	
	$query = "UPDATE `bank` SET `onHand`= `onHand` + '".$costtosell."' WHERE `uid` =".$_SESSION['userid']." LIMIT 1";
	mysql_query($query);
	echo "weapons sold for ".number_format($costtosell)." Naquadah.";
	$query = "DELETE FROM `weapons` WHERE `uid` =".$_SESSION['userid']." AND `wid`=".$wid." AND `quanity` = '0' LIMIT 1";
	mysql_query($query);
	}
}

if(isset($_POST['submit2']) && $_POST['submit2'] != "Submit")
{
	$posted = array();
	if (isset($weapons['atk'])) {
		for ($x = 0; $x < count($weapons['atk']); $x++)
		{
			$posted[$weapons['atk'][$x]['fieldname']] = $_POST[$weapons['atk'][$x]['fieldname']];
		}
	}
	if (isset($weapons['def'])) {
		for ($x = 0; $x < count($weapons['def']); $x++)
		{
			$posted[$weapons['def'][$x]['fieldname']] = $_POST[$weapons['def'][$x]['fieldname']];
		}
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
      <?php if (isset($inv['atk'])) { for ($x = 0; $x < count($inv['atk']); $x++){?>
	  <tr>
        <td align="left" valign="middle"><?php echo $inv['atk'][$x]['name'];?></td>
        <td align="center" valign="middle"><?php echo $inv['atk'][$x]['quanity'];?></td>
		<td align="center" valign="middle"><?php echo $inv['atk'][$x]['strength']."/".$inv['atk'][$x]['power'];?></td>
        <td align="center" valign="middle"><a href="javascript:void(0)" onclick="sendData('armory','get','<?php echo $inv['atk'][$x]['wid'];?>','repair'); return false;"><?php echo $inv['atk'][$x]['perpoint'];?></a></td>
        <td align="center" valign="middle"><input name="<?php echo $inv['atk'][$x]['fieldname'];?>" id="<?php echo $inv['atk'][$x]['fieldname'];?>" type="text" value="0" size="10" /><a href="javascript:void(0)" onclick="sendData('armory', return false;"> for <?php echo $inv['atk'][$x]['sell'];?> each</a></td>        <td colspan="4" align="right" valign="bottom"><input type="submit" name="buyweaps" value="for <?php echo $inv['atk'][$x]['sell'];?> each" onclick="this.value='Selling Weapons...'; this.disabled=true; sendData('armory','post',<?php echo $inv['atk'][$x]['fieldname'];?>.value,'sellweps','<?php echo $inv['atk'][$x]['wid'];?>')"/>
      </tr>
	  <?php }} ?>
	  <tr><td>&nbsp;</td></tr>
      <tr>
        <td align="left" valign="middle">Defense Weapons</td>
        <td align="center" valign="middle">Quanity</td>
        <td align="center" valign="middle">Strength</td>
        <td align="center" valign="middle">Repair</td>
        <td align="center" valign="middle">Scrap /Sell </td>
      </tr>
      <?php if (isset($inv['def'])) { for ($x = 0; $x < count($inv['def']); $x++){?>
	  <tr>
        <td align="left" valign="middle"><?php echo $inv['def'][$x]['name'];?></td>
        <td align="center" valign="middle"><?php echo $inv['def'][$x]['quanity'];?></td>
		<td align="center" valign="middle"><?php echo $inv['def'][$x]['strength']."/".$inv['def'][$x]['power']; ?></td>
        <td align="center" valign="middle"><a href="javascript:void(0)" onclick="sendData('armory','get','<?php echo $inv['def'][$x]['wid'];?>','repair'); return false;"><?php echo $inv['def'][$x]['perpoint'];?></a></td>
        <td align="center" valign="middle"><input name="<?php echo $inv['def'][$x]['fieldname'];?>" id="<?php echo $inv['def'][$x]['fieldname'];?>" type="text" value="0" size="10" /><a href="javascript:void(0)" onclick="sendData('armory','get',<?php echo $inv['def'][$x]['fieldname'];?>.value,'sellweps','<?php echo $inv['def'][$x]['wid'];?>');"> for <?php echo $inv['def'][$x]['sell'];?> each</a></td>
      </tr>
	  <?php }} ?>
	  <tr><td>&nbsp;</td></tr>
    </table></td>
  </tr>
  <tr>
    <td width="37%" align="left" valign="top"><?php include_once('mil_rank.php'); echo "<br>"; include_once('personnel.php'); ?>
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
	  if (isset($weapons['atk'])) {
	  for ($x = 0; $x < count($weapons['atk']); $x++)
	  {
	  	if($weapons['atk'][$x]['unitcost'] ==0 && !$weapons['atk'][$x]['cashcost'] ==0)
		{
	    ?>
	  <tr>
        <td width="26%" align="left" valign="top"><?php echo $weapons['atk'][$x]['name'];?></td>
        <td width="18%" align="right" valign="top"><?php echo $weapons['atk'][$x]['power'];?></td>
        <td width="40%" align="right" valign="top"><?php echo $weapons['atk'][$x]['cashcost'];?> naquadrea</td>
        <td width="16%" align="right" valign="bottom"><input name="<?php echo $weapons['atk'][$x]['fieldname'];?>" type="text" value="0" size="10" /></td>
      </tr>
        <?php
	  	}elseif ($weapons['atk'][$x]['cashcost'] ==0 && !$weapons['atk'][$x]['unitcost'] ==0 ){
	    ?>
	  <tr>
        <td width="26%" align="left" valign="top"><?php echo $weapons['atk'][$x]['name'];?></td>
        <td width="18%" align="right" valign="top"><?php echo $weapons['atk'][$x]['power'];?></td>
        <td width="40%" align="right" valign="top"><?php echo $weapons['atk'][$x]['unitcost'];?> untrained units</td>
        <td width="16%" align="right" valign="bottom"><input name="<?php echo $weapons['atk'][$x]['fieldname'];?>" type="text" value="0" size="10" /></td>
      </tr>
	  	<?php
		}else if(!$weapons['atk'][$x]['cashcost'] ==0 && (!$weapons['atk'][$x]['unitcost'] ==0)){		
		?>
	  <tr>
        <td width="26%" align="left" valign="top"><?php echo $weapons['atk'][$x]['name'];?></td>
        <td width="18%" align="right" valign="top"><?php echo $weapons['atk'][$x]['power'];?></td>
        <td width="40%" align="right" valign="top"><?php echo $weapons['atk'][$x]['cashcost'];?> naquadrea and<br /> 
          <?php echo $weapons['atk'][$x]['unitcost'];?> untrained units</td>
        <td width="16%" align="right" valign="bottom"><input name="<?php echo $weapons['atk'][$x]['fieldname'];?>" type="text" value="0" size="10" /></td>
      </tr>
	  <?php
	  }
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
	  if (isset($weapons['def'])) {
	  for ($x = 0; $x < count($weapons['def']); $x++)
	  {
	  	if($weapons['def'][$x]['unitcost'] ==0  && !$weapons['def'][$x]['cashcost'] ==0 )
		{
	    ?>
	  <tr>
        <td width="26%" align="left" valign="top"><?php echo $weapons['def'][$x]['name'];?></td>
        <td width="18%" align="right" valign="top"><?php echo $weapons['def'][$x]['power'];?></td>
        <td width="40%" align="right" valign="top"><?php echo $weapons['def'][$x]['cashcost'];?> naquadrea</td>
        <td width="16%" align="right" valign="bottom"><input name="<?php echo $weapons['def'][$x]['fieldname'];?>" type="text" value="0" size="10" /></td>
      </tr>
        <?php
	  	}elseif ($weapons['def'][$x]['cashcost'] ==0 && !$weapons['def'][$x]['unitcost'] ==0  ){
	    ?>
	  <tr>
        <td width="26%" align="left" valign="top"><?php echo $weapons['def'][$x]['name'];?></td>
        <td width="18%" align="right" valign="top"><?php echo $weapons['def'][$x]['power'];?></td>
        <td width="40%" align="right" valign="top"><?php echo $weapons['def'][$x]['unitcost'];?> untrained units</td>
        <td width="16%" align="right" valign="bottom"><input name="<?php echo $weapons['def'][$x]['fieldname'];?>" type="text" value="0" size="10" /></td>
      </tr>
	  	<?php
		}else if(!$weapons['def'][$x]['cashcost'] ==0 && (!$weapons['def'][$x]['unitcost'] ==0)){		
		?>
	  <tr>
        <td width="26%" align="left" valign="top"><?php echo $weapons['def'][$x]['name'];?></td>
        <td width="18%" align="right" valign="top"><?php echo $weapons['def'][$x]['power'];?></td>
        <td width="40%" align="right" valign="top"><?php echo $weapons['def'][$x]['cashcost'];?> naquadrea and <br />
          <?php echo $weapons['def'][$x]['unitcost'];?> untrained units</td>
        <td width="16%" align="right" valign="bottom"><input name="<?php echo $weapons['def'][$x]['fieldname'];?>" type="text" value="0" size="10" /></td>
      </tr>
	  <?php
	  }
	 }
	  }
	  ?>
	  <tr><td>&nbsp;</td></tr>
      <tr>
        <td colspan="4" align="right" valign="bottom"><input type="submit" name="buyweaps" value="Submit" onclick="this.value='Buying Weapons...'; this.disabled=true; sendData('armory','post','<?php echo $s->uid;?>')"/></td>
        </tr>
    </table

></table>
<?php
echo "Query Count: ".$s->queryCount."<br>";
$pagegen->stop();
print('page generation time: ' . $pagegen->gen());
?>