<?
include_once("../config.php");
$pagegen = new page_gen();
$pagegen->round_to = 4;
$pagegen->start();
$s = new Game();

if($_GET['id'] == "deposit" || $_GET['id'] == "withdrawl")
{
	$s->bank($_GET['id'],$_GET['atype']);
}
$data = $s->bank();
?>
<form action="javascript:void(0)">
Your Bank Account:<br /><br />
<table width="100%" border="0">
  <tr>
    <td width="23%">Naquadah on Hand </td>
    <td width="23%">Naquadah in Bank </td>
    <td width="27%"> Bank Account Capacity </td>
    <td width="27%">Space Left </td>
  </tr>
  <tr>
    <td><?= number_format($data->onHand); ?></td>
    <td><?= number_format($data->inBank); ?></td>
    <td><?= number_format($data->cap); ?></td>
    <td><? if($data->left < 0) { echo "0"; } else { echo number_format($data->left); } ?></td>
  </tr>
  <tr>
    <td>Put Naquadah into Account:</td>
    <td align="left">ammount: <input id="deposit" name="deposit" type='text' value="<?= number_format($data->onHand,0,'',''); ?>" size="10" /></td>
    <td colspan="2" align="left" valign="top"><input type="submit" name="giveThis" value="Deposit" onClick="this.disabled=true; this.value='Depositing'; sendData('bank','get','deposit',deposit.value);" /></td>
  </tr>
  <tr>
    <td>Take Naquadah out of Account:</td>
    <td align="left">ammount:
    <input id="withdrawl" name="withdrawl" type='text' value="0" size="10" /></td>
    <td colspan="2" align="left" valign="top"><input type="submit" name="takeThis" value="Withdrawl" onClick="this.disabled=true; this.value='Depositing'; sendData('bank','get','withdrawl',withdrawl.value);" /></td>
  </tr>
</table>
</form>
<br /><br />
<?
echo "Query Count: ".$s->queryCount."<br>";
$pagegen->stop();
print('page generation time: ' . $pagegen->gen());
?>