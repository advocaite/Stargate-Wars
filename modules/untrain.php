<?
include_once("../config.php");
$s = new Game();
$train = $s->getPersonnel($_SESSION['userid']);
?>
<form action="javascript:void(0)" name="untrain">
      <table width="100%" border="0">
	    <tr>
          <td colspan="3" align="center" valign="top">Untrain Units </td>
        </tr>
        <tr>
          <td align="left">&nbsp;</td>
          <td align="left">Cost</td>
          <td align="left">Quanity</td>
        </tr>
        <tr>
          <td align="left">Reassign Miners</td>
          <td align="left">0</td>
          <td align="left"><input type="text" name="resmin" value='0'/></td>
        </tr>
        <tr>
          <td align="left">Reassign
<?= $train->attackName; ?></td>
          <td align="left">0</td>
          <td align="left"><input type="text" name="resatk" value='0'/></td>
        </tr>
        <tr>
          <td align="left">Reassign
<?= $train->defenseName; ?></td>
          <td align="left">0</td>
          <td align="left"><input type="text" name="resdef" value='0' /></td>
        </tr>
        <tr>
          <td align="left">Reassign
<?= $train->covertName; ?></td>
          <td align="left">0</td>
          <td align="left"><input type="text" name="rescov" value='0' /></td>
        </tr>
        <tr>
          <td align="left">Reassign
<?= $train->anticovertName; ?></td>
          <td align="left">0</td>
          <td align="left"><input type="text" name="resanti" value='0' /></td>
        </tr>

        <tr>
          <td colspan="3" align="center"><input type="submit" name='unt' value='Reassign' onclick="this.value='Ressigning...'; this.disabled=true; sendData('train','post','untrn');"/></td>
        </tr>
  </table>
</form>