<? 
include("../config.php");

$pagegen = new page_gen();
$pagegen->round_to = 4;
$pagegen->start();

$s = new Game();
if (!$s->loggedIn){ header("Location: https://realmbattles.org/SGWnew/index.php?"); }
$s->updatePower($_SESSION['userid']);
if($_GET['id'] != "mainDisplay")
{
	$s->buyTech($_GET['id'],$_GET['atype']);
}
$buy = $s->fieldtocrypt();

$tech = $s->viewTech();
if($tech->ttl>=0 && $tech->ttl<=200)
{
	$_SESSION['progress'] = $tech->ttl;
}elseif($tech->ttl>200)
{
	$_SESSION['progress'] =200;
}
$data = $s->level($tech->ascend);
$tech->ascend++;
$data["z"] = number_format($data["y"]*$tech->ttl);
$a = 0;

?>
<form action="javascript:void(0)">

<center>
<table border='0'>
  <colgroup id="name"><col width="35%" /><col width="20%" /><col width="10%" /><col width="10%" /><col width="25%" /></colgroup>
<tr>
  <td colspan="4"><? include_once("progressinfo.php"); ?>
    </td>
  </tr>
<tr>
	<td>Name of Upgrade</td>
	<td align="left">Status</td>
	<td align="center">Current Level</td>
	<td align="center">Max Level</td>
	<td align="center">Upgrade</td>
</tr>
<tr>
	<td>Unit Production Per Turn:<br />
		<font>-Standard is 3 Per Level. You Can Increase this by increasing Units Per Unit Production Level</font></td>
	<td align="left"><? $status = (3+$tech->uppl)*$tech->unitProd; echo $status; ?> units</td>
	<td align="center"><?= number_format($tech->unitProd);?></td>
	<td align="center"><? $upmax = ($tech->ascend*500); echo number_format($upmax); ?></td>
	<td align="center"><? if($tech->unitProd  < $upmax) { ?><input name='buyup' type="text" value='1' size="4" maxlength="4">
	<input type="button" name="submit1" value="<? $upcost = ((($tech->ascend)*5000000)*($tech->unitProd)); echo number_format($upcost)." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buyup.value);"><? }else{ $a++;?> Capacity Reached. <? } ?> </td>
</tr>
<tr>
	<td>Units Per Unit Production Level<br />
		<font>-Increase how many units you get per Unit Production Level</font></td>
	<td align="left"><?= number_format($tech->uppl+3);?> units</td>
	<td align="center"><?= number_format($tech->uppl);?></td>
	<td align="center"><? $upmax = $tech->ascend*10; echo number_format($upmax); ?></td>
	<td align="center"><? if($tech->uppl  < $upmax) { ?> <input name='buyuppl' type="text" value='1' size="4" maxlength="3">
	<input type="button" name="submit2" value="<? $upcost = ((($tech->ascend)*50000000)*($tech->uppl+1)); echo number_format($upcost)." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buyuppl.value);"><? }else{ $a++;?> Capacity Reached. <? } ?>  </td>
</tr>
<tr>
	<td>Income per Unit:<br />
		<font>-Income Per Untrained Unit. Standard is 80 Per Miner/Lifer.</font></td>
	<td align="left"><?= number_format($tech->income+80); ?>  Naquadah</td>
	<td align="center"><?= number_format($tech->income); ?></td>
	<td align="center"><? $upmax = $tech->ascend*10; echo number_format($upmax); ?></td>
	<td align="center"><? if($tech->income  < $upmax) { ?><input name='buyinc' type="text" value='1' size="4" maxlength="3">
	<input type="button" name="submit3" value="<? $upcost = ((($tech->ascend)*10000000)*($tech->income+1)); echo number_format($upcost)." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buyinc.value);"><? }else{ $a++;?> Capacity Reached. <? } ?> </td>
</tr>
<tr>
	<td>Galaxy Size:<br />
		<font>-Number of Planets Your Galaxy can Hold.</font></td>
	<td align="left"><?= number_format(10+$tech->galaxy); ?> Planets</td>
	<td align="center"><?= number_format($tech->galaxy); ?></td>
	<td align="center"><? $max=0;?> TBC</td>
	<td align="center"><? if($tech->galaxy  < $max) { ?><input name='buygal' type="text" id="buygal" value='1' size="4" maxlength="3" />
	<input type="button" name="submit4" value="<?="TBC"." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buygal.value);"><? }else{ $a++;?> Capacity Reached. <? } ?> </td>
</tr>
<tr>
	<td>Planetary Unit Capacity:<br />
		<font>The ammount of Untrained Units A Planet can Hold</font></td>
	<td align="left"><?= number_format(500000+($tech->puCap*50000)); ?> Untrained Units</td>
	<td align="center"><?= number_format($tech->puCap); ?></td>
	<td align="center"><? $max = 0; ?>TBC</td>
	<td align="center"><? if($tech->puCap  < $max) { ?><input name='buypu' type="text" id="buypu" value='1' size="4" maxlength="3" />
	<input type="button" name="submit5" value="<?="TBC"." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buypu.value);"><? }else{ $a++;?> Capacity Reached. <? } ?> </td>
</tr>
<tr>
	<td>Planetary Miner Capacity:<br />
		<font>-Ammount of Miner/Lifers a Planet can hold.</font></td>
	<td align="left"><?= number_format(50000+($tech->pmCap*25000)); ?> Miners </td>
	<td align="center"><?= number_format($tech->pmCap); ?></td>
	<td align="center"><? $max=0; ?> TBC</td>
	<td align="center"><? if($tech->pmCap  < $max) { ?><input name='buypm' type="text" id="buypm" value='1' size="4" maxlength="3" />
	<input type="button" name="submit6" value="<?="TBC"." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buypm.value);"><? }else{ $a++;?> Capacity Reached. <? } ?> </td>
</tr>
</table><br /><br />
<table border='0'>
  <colgroup id="name"><col width="35%" /><col width="15%" /><col width="15%" /><col width="35%" /></colgroup>
<tr>
	<td>Name of Upgrade</td>
	<td align="center">Current Level</td>
	<td align="center">Max Level</td>
	<td align="center">Upgrade</td>
</tr>
<tr>
	<td>Attack Unit Strength:<br />
		<font>-Increases Power Per Attack Unit.</font></td>
	<td align="center"><?= number_format($tech->attack); ?></td>
	<td align="center"><?= $data["x"]; ?></td>
	<td align="center"><? if($tech->attack  < $data["x"]) { ?><input name='buyatk' type="text" id="buyatk" value='1' size="4" maxlength="3" />
	<input type="button" name="submit7" value="<?= $data["z"]." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buyatk.value);"><? }else{ $a++;?> Capacity Reached. <? } ?> </td>
</tr>
<tr>
	<td>Attack Unit Resourcefulness:<br />
		<font>-How Much A unit can steal from The Enemy.</font></td>
	<td align="center"><?= number_format($tech->auSteal); ?></td>
	<td align="center"><?= $data["x"]; ?></td>
	<td align="center"><? if($tech->auSteal  < $data["x"]) { ?><input name='buyatksteal' type="text" id="buyatksteal" value='1' size="4" maxlength="3" />
	<input type="button" name="submit8" value="<?= $data["z"]." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buyatksteal.value);"><? }else{ $a++;?> Capacity Reached. <? } ?> </td>
</tr>
<td>Attack Unit Resourcefulness:<br />
		<font>-How much of an enemy your units kill</font></td>
	<td align="center"><?= number_format($tech->auEffect); ?></td>
	<td align="center"><?= $data["x"]; ?></td>
	<td align="center"><? if($tech->auEffect  < $data["x"]) { ?><input name='buyatkEff' type="text" id="buyatkEff" value='1' size="4" maxlength="3" />
	<input type="button" name="submit9" value="<?= $data["z"]." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buyatkEff.value);"><? }else{ $a++;?> Capacity Reached. <? } ?> </td>
<tr>
	<td>Attack Unit Resilence:<br />
		<font>-Decrease the ammount of Death of your Attack Units.</font></td>
	<td align="center"><?= number_format($tech->auRes); ?></td>
	<td align="center"><?= $data["x"]; ?></td>
	<td align="center"><? if($tech->auRes  < $data["x"]) { ?><input name='buyatkRes' type="text" id="buyatkRes" value='1' size="4" maxlength="3" />
	<input type="button" name="submit0" value="<?= $data["z"]." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buyatkRes.value);"><? }else{ $a++;?> Capacity Reached. <? } ?> </td>
</tr>
<tr>
	<td>Defense Unit Strength:<br />
		<font>-Increases Power Per Defense Unit.</font></td>
	<td align="center"><?= number_format($tech->defense); ?></td>
	<td align="center"><?= $data["x"]; ?></td>
	<td align="center"><? if($tech->defense  < $data["x"]) { ?><input name='buydef' type="text" id="buydef" value='1' size="4" maxlength="3" />
	<input type="button" name="submit11" value="<?= $data["z"]." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buydef.value);"><? }else{ $a++;?> Capacity Reached. <? } ?> </td>
</tr>
<tr>
	<td>Defense Unit Resourcefulness:<br />
		<font>-How much the enemy can Steal from you.</font></td>
	<td align="center"><?= number_format($tech->duSteal); ?></td>
	<td align="center"><?= $data["x"]; ?></td>
	<td align="center"><? if($tech->duSteal  < $data["x"]) { ?><input name='buydefSteal' type="text" id="buydefSteal" value='1' size="4" maxlength="3" />
	<input type="button" name="submit12" value="<?= $data["z"]." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buydefSteal.value);"><? }else{ $a++;?> Capacity Reached. <? } ?> </td>
</tr>
	<td>Defense Unit Effectiveness:<br />
		<font>-How much of the enemy die in an action</font></td>
	<td align="center"><?= number_format($tech->duEffect); ?></td>
	<td align="center"><?= $data["x"]; ?></td>
	<td align="center"><? if($tech->duEffect  < $data["x"]) { ?><input name='buydefEff' type="text" id="buydefEff" value='1' size="4" maxlength="3" />
	<input type="button" name="submit13" value="<?= $data["z"]." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buydefEff.value);"><? }else{ $a++;?> Capacity Reached. <? } ?> </td>
<tr>
	<td>Defense Unit Resilence:<br />
		<font>-Decrease the ammount of Death of your Defense Units.</font></td>
	<td align="center"><?= number_format($tech->duRes); ?></td>
	<td align="center"><?= $data["x"]; ?></td>
	<td align="center"><? if($tech->duRes  < $data["x"]) { ?><input name='buydefRes' type="text" id="buydefRes" value='1' size="4" maxlength="3" />
	<input type="button" name="submit14" value="<?= $data["z"]." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buydefRes.value);"><? }else{ $a++;?> Capacity Reached. <? } ?> </td>
</tr>
<tr>
	<td>Covert Unit Strength:<br />
		<font>-Increases Power Per Covert Unit.</font></td>
	<td align="center"><?= number_format($tech->covert); ?></td>
	<td align="center"><?= $data["x"]; ?></td>
	<td align="center"><? if($tech->covert  < $data["x"]) { ?><input name='buycov' type="text" id="buycov" value='1' size="4" maxlength="3" />
	<input type="button" name="submit15" value="<?= $data["z"]." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buycov.value);"><? }else{ $a++;?> Capacity Reached. <? } ?> </td>
</tr>
<tr>
	<td>Covert Unit Effectiveness:<br />
		<font>-How Much the covert unit can kill of the enemy.</font></td>
	<td align="center"><?= number_format($tech->cuEffect); ?></td>
	<td align="center"><?= $data["x"]; ?></td>
	<td align="center"><? if($tech->cuEffect  < $data["x"]) { ?><input name='buycovEff' type="text" id="buycovEff" value='1' size="4" maxlength="3" />
	<input type="button" name="submit16" value="<?= $data["z"]." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buycovEff.value);"><? }else{ $a++;?> Capacity Reached. <? } ?> </td>
</tr>
<tr>
	<td>Covert Unit Resilence:<br />
		<font>-Decrease the ammount of Death of your Covert Units.</font></td>
	<td align="center"><?= number_format($tech->cuRes); ?></td>
	<td align="center"><?= $data["x"]; ?></td>
	<td align="center"><? if($tech->cuRes  < $data["x"]) { ?><input name='buycovRes' type="text" id="buycovRes" value='1' size="4" maxlength="3" />
	<input type="button" name="submit17" value="<?= $data["z"]." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buycovRes.value);"><? }else{ $a++;?> Capacity Reached. <? } ?> </td>
</tr>
<tr>
	<td>Anticovert Unit Strength:<br />
		<font>-Increases Power Per AntiCovert Unit.</font></td>
	<td align="center"><?= number_format($tech->anticovert); ?></td>
	<td align="center"><?= $data["x"]; ?></td>
	<td align="center"><? if($tech->anticovert  < $data["x"]) { ?><input name='buyanti' type="text" id="buyanti" value='1' size="4" maxlength="3" />
	<input type="button" name="submit18" value="<?= $data["z"]." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buyanti.value);"><? }else{ $a++;?> Capacity Reached. <? } ?> </td>
</tr>
<tr>
	<td>Anticovert Unit Effectiveness:<br />
		<font>-How many operatives you can stop from accessing your inteligence networks.</font></td>
	<td align="center"><?= number_format($tech->acuEffect); ?></td>
	<td align="center"><?= $data["x"]; ?></td>
	<td align="center"><? if($tech->acuEffect  < $data["x"]) { ?><input name='buyantiEff' type="text" id="buyantiEff" value='1' size="4" maxlength="3" />
	<input type="button" name="submit19" value="<?= $data["z"]." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buyantiEff.value);"><? }else{ $a++;?> Capacity Reached. <? } ?> </td>
</tr>
<tr>
	<td>Anticovert Unit Resilence:<br />
		<font>-Decrease the ammount of Death of your Anticovert Units.</font> 	</td>
	<td align="center"><?= number_format($tech->acuRes); ?></td>
	<td align="center"><?= $data["x"]; ?></td>
	<td align="center"><? if($tech->acuRes  < $data["x"]) { ?><input name='buyantiRes' type="text" id="buyantiRes" value='1' size="4" maxlength="3" />
	<input type="button" name="submit20" value="<?= $data["z"]." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buyantiRes.value);"><? }else{ $a++;?> Capacity Reached. <? } ?> </td>
</tr>
<tr>
  <td>Covert Level<br />
	  <font>-The Overall Level of Your Covert Power</td>
  <td align="center"><?= $tech->cov_lvl; ?></td>
  <td align="center">None</td>
  <td align="center"><input type="button" name="submit20" value="<? $cost = 15000; for($x=0; $x < $tech->cov_lvl; $x++){ $cost *=2; }; echo number_format($cost)." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>','1');"></td>
</tr>
<tr>
  <td>Anti-Covert Level<br />
	  <font>-The Overall Level of Your Anti-Covert Power</td>
  <td align="center"><?= $tech->anti_lvl; ?></td>
  <td align="center">None</td>
  <td align="center"><input type="button" name="submit20" value="<? $cost = 15000; for($x=0; $x < $tech->anti_lvl; $x++){ $cost *=2; }; echo number_format($cost)." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>','1');"></td>
</tr>
<tr>
	<td>Planet Defensive Effectiveness:<br />
		<font>-The Power your planetary defenses have against the Enemy</font></td>
	<td align="center"><?= number_format($tech->pDef); ?></td>
	<td align="center"><?= $data["x"]; ?></td>
	<td align="center"><? if($tech->pDef  < $data["x"]) { ?><input name='buypDef' type="text" id="buypDef" value='1' size="4" maxlength="3" />
	<input type="button" name="submit21" value="<?= $data["z"]." Naquadah"; ?>" onClick="this.value='Upgrading'; this.disable=true;  sendData('technology','post','<?= $buy[$a++];?>',buypDef.value);"><? }else{ $a++;?> Capacity Reached. <? } ?> </td>
</tr>
<tr>
	<td>Ascension:<br />
		<font>-Your overall Awareness and Capacity for understanding the universe</font></td>
	<td align="center"><?= $data["str"]." (".($tech->ascend-1).")"; ?></td>
	<td align="center">6</td>
	<td align="center"><? if($tech->ascend  < 6) { ?><input type="button" name="submit22" value="<?= $data["z"]." Naquadah"; ?>" onClick=" this.value='Does not Work'; this.disable=true; <? /*sendData('technology','post','<?= $buy[$a++];?>'); */ ?>"><? }else{ ?> Can't Ascend Any Further. <? } ?> </td>
</tr>
</table>
</form>
</center><?
echo "Query Count: ".$s->queryCount."<br>";
$pagegen->stop();
print('page generation time: ' . $pagegen->gen());
?>