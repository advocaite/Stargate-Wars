<? 
include("../config.php");

$pagegen = new page_gen();
$pagegen->round_to = 4;
$pagegen->start();
$s = new Game();
$s->updatePower($_SESSION['userid']); 
if($_GET['id'] && $_GET['atype']!="Send")
{
	$query = "SELECT `uname` FROM `users` WHERE uid=".$_GET['id']." LIMIT 1";
	$q = $s->query($query);
	$data = mysql_fetch_object($q);
	$name = $data->uname;
	$id = $_GET['id'];
}

if($_REQUEST['atype']=="Send")
{
	if($s->create_allliance($_GET['id'],$_REQUEST['subject'],$_REQUEST['message'],$_REQUEST['url'],$_REQUEST['allow']))
	{
		echo ",Thankyou";
	}
	else
	{
		echo ",If problem persit Contact Admin";
	}
}
?>
<form action="javascript:void(0)" onSubmit="submit.value='Sending Message'; submit.disabled=true; sendData('c_ally','post',userID.value,'Send');"><center>
<input type="hidden" id="userID" name="userID" value="<?= $id; ?>">
<table width="100%" border="0">
  <tr>
    <td align="left" valign="top">Alliance Name:
   </td>
    <td colspan="3" align="left" valign="top"><input type="text" name="subject"></td>
  </tr>
  
  <tr>
    <td align="left" valign="top">Alliance Description:</td>
    <td colspan="3" align="left" valign="top">
      <p>:

        <textarea name="message" cols="100" rows="20" wrap="virtual"></textarea>

        <br>
      </p>
   </td>
  </tr>
  <tr>
        <td><div align="center">Alliance URL: http:// </div></td>
        <td><input name="url" type="text" id="url" value="http://"></td>
      </tr>
      <tr>
        <td><div align="center">Dont Allow  New Members? </div></td>
        <td><input name="allow" type="checkbox" id="allow" value="1"></td>
      </tr>
  
</table>
<input type="submit" name="submit" id="submit" value="create alliance">
</center>
 </form>
 <?

echo "Query Count: ".$s->queryCount."<br>";
$pagegen->stop();
print('page generation time: ' . $pagegen->gen());

?>