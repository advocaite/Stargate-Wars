<?
include_once("../config.php");

$pagegen = new page_gen();
$pagegen->round_to = 4;
$pagegen->start();

$s = new Game();
if (!$s->loggedIn || !$_GET['time']){ header("Location: https://realmbattles.org/SGWnew/index.php?"); }
$s->updatePower($_SESSION['userid']);
$base = $s->baseVars();
if ($base->allyid != 0 ){
$allyinfo = $s->getallyinfo($base->allyid);}
$newsQ = mysql_query("SELECT * FROM news ORDER BY id DESC") or die(mysql_error());
?>
<table align="center" border="0" cellpadding="10" cellspacing="0" width="90%">
<?
while ($news = mysql_fetch_array($newsQ)) {
$datenews = date('jS M y, G:i', ($news[news_time]+3600*$logged[cas_modif]));
echo "<tr>
<td class=\"news1\"><font color\"yellow\">".$news[news_naslov]." </font>(posted by <font color\"yellow\">".$news[user_name]."</font> at ".$datenews.")</td>
</tr>
<tr>
<td class=\"news2\">".$news[news_text]."</td>
</tr>
<tr><td></td></tr>";
}
echo "</table>";?>
<table width="100%" border="0">
  <tr>
    <td width="58%" align="center" valign="top"><table width="100%" border="0">
      <tr>
        <td width="39%" align="left" valign="top" bordercolor="#000000">Name [ID]</td>
        <td width="61%" align="left" valign="top" bordercolor="#000000"><?= $base->uname." [ ".$_SESSION['userid']." ]"; ?></td>
      </tr>
      <tr>
        <td align="left" valign="top" bordercolor="#000000">E-mail <?= $_SESSION['userid']; ?></td>
        <td align="left" valign="top" bordercolor="#000000"><?= $base->email; ?></td>
      </tr>
      <tr>
        <td align="left" valign="top" bordercolor="#000000">Race</td>
        <td align="left" valign="top" bordercolor="#000000"><?= $base->r_name; ?></td>
      </tr>
      <tr>
        <td align="left" valign="top" bordercolor="#000000">Commander</td>
        <td align="left" valign="top" bordercolor="#000000"><a href="javascript:void(0)" onclick="sendData('user','get','<?= $base->cid; ?>'); return false"><?= $base->cname; ?></a></td>
      </tr>
      <tr>
        <td align="left" valign="top" bordercolor="#000000">HomePlanet Name </td>
        <td align="left" valign="top" bordercolor="#000000"><?= $base->plnt_name; ?></td>
      </tr>
      <tr>
        <td align="left" valign="top" bordercolor="#000000">HomePlanet Size </td>
        <td align="left" valign="top" bordercolor="#000000"><?= $base->text; ?></td>
      </tr>
      <tr>
        <td align="left" valign="top" bordercolor="#000000">Total Planets Owned </td>
        <td align="left" valign="top" bordercolor="#000000"><?= number_format($base->ttlPlanetsOwned); ?></td>
      </tr>
      <tr>
        <td align="left" valign="top" bordercolor="#000000">Unit Production </td>
        <td align="left" valign="top" bordercolor="#000000"><?= number_format($base->up); ?> a Turn</td>
      </tr>
      <tr>
        <td align="left" valign="top" bordercolor="#000000">Turn Income Production </td> 
        <td align="left" valign="top" bordercolor="#000000"><?= number_format($base->income); ?> Naquadah</td>
      </tr>
      <tr>
        <td colspan="2" align="left" valign="top">Defense and Covert Alert Level *TBC</td>
        </tr>
    </table><br /><table width="100%" border="0">
	<?Debug::printMsg(__CLASS__, __FUNCTION__, "Start of alliance info");?>
  <tr>
    <td colspan="3" align="left">Alliance Management *TBC </td>
    </tr>
  <tr>
    <td colspan="3" align="left">List of Alliances </td>
    </tr>
  <tr>
    <td width="12%" align="left">Alliance: </td>
    <td width="38%" align="left"><?if ($base->allyid == 0 ){echo "None</td>";} else { ?><a href="javascript:void(0)" onclick="sendData('ally_mlist','get','<?= $base->allyid; ?>'); return false"><?= $allyinfo->allyname ?></a></td><?}?>
   <?if ($base->allyid == 0 ){?><td width="50%" align="left"><a href="javascript:void(0)" onclick="sendData('c_ally','get','<?= $base->uid; ?>'); return false;">Creat Alliance </a></td><?}else{?>
	<td width="50%" align="left"><input type="submit" name='allyenter' value='Enter Alliance' onclick="this.value='What ever...'; this.disabled=true; sendData('ally_mlist','get','<?=$base->allyid; ?>');" /></td><?}?>
  </tr>
</table><br /><table width="100%" border="0">
  <tr>
    <td colspan="5" align="left">Office Management</td>
    </tr>
  <tr>
    <td colspan="2" align="left">Accept New Officers?: </td>
    <td colspan="1.5" align="left">&nbsp;</td>
    <td colspan="1.5" align="left">&nbsp;</td>
  </tr>
  <tr><td align="left">Name</td>
  <td align="left">Army Size</td>
  <td align="left">Mercenaries</td>
  <td align="left">Race</td>
  <td align="left">Rank</td>
  </tr>
  <?
  $offi = $s->getOfficers($_SESSION['userid']);
  for($x=0; $x<count($offi); $x++)
  {
  	echo "<tr><td><a href=\"javascript:void(0)\" onclick=\"sendData('user','get','".$offi[$x]["uid"]."'); return false\">".$offi[$x]["name"]."</a>
		  </td><td>".number_format($offi[$x]["size"])."</td><td>".number_format($offi[$x]["mercs"])."</td><td>"
		  .$offi[$x]["race"]."</td><td>".$offi[$x]["rank"]."</td></tr>";
		  
  }
      echo "<tr> <td colspan='5'>Number of Officers: ".count($offi)."</td> </tr>"
  ?>
</table></td>
    <td width="42%" align="center" valign="top"><? include_once('mil_rank.php'); ?><br />
      <? include_once('./personnel.php'); ?></td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle"><table width="58%" border="0">
      <tr>
        <td align="center" valign="top">Recruitment</td>
      </tr>
      <tr>
        <td align="center" valign="top">Post this link around get more players and more army for your self spread the word a ragging war is going on here</td>
      </tr>
      <tr>
        <td align="center" valign="top"><a href="javascript:void(0)" onclick="sendData('recruit','get','<?= $base->link; ?>'); return false">http://localhost/recruit.php?link=<?= $base->link; ?></a></td> </td>
      </tr>
    </table></td>
  </tr>
</table>

<?
$pagegen->stop();
echo "Query Count: ".$s->queryCount."<br>";
print('page generation time: ' . $pagegen->gen());
?>