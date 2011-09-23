<? 
include("../config.php");

$pagegen = new page_gen();
$pagegen->round_to = 4;
$pagegen->start();

$s = new Game();
$s->updatePower($_SESSION['userid']);
if($_REQUEST['atype']=="delete")
{
	if($s->deleteMessage($_REQUEST['id']))
	{
		echo "Deleted Message(s)";
	}else{ echo "Error Deleting Message(s)"; }
}
if($_REQUEST['atype']=="userDel")
{
	$query = "DELETE FROM messages WHERE fromUID=".$_REQUEST['id']." AND toUID=".$_SESSION['userid'];
	if($s->query($query))
	{
		echo "Deleted Message(s)";
	}
	else
	{
		echo "Error Deleting Contact Admin";
	}
}
?>

<table width="100%" border="0">
<tr><td width="15%">User</td>
<td width="14%">Subject</td>
<td>BOdy</td><td width="9%">TimeSent</td>
<td width="9%"></td><td>&nbsp;</td></tr>
<?
$q = $s->viewMessages();
while ($data = mysql_fetch_object($q))
{
?>	
<tr><td><a href="javascript:void(0);" onclick="sendData('user','get','<?= $data->fromUID; ?>');"><?= $data->user; ?></a></td><td><?= $data->subject; ?></td><td width="53%"><table border="0"><tr><td><a href="javascript:void(0)" onclick="toggle_visible('data<?= $data->mid; ?>');">Message</a></td><td>
    <div id="data<?= $data->mid; ?>" style="visibility:hidden; display:inline;"><?= $data->message; ?></div></td></tr></table></td><td><?= $data->timeSent; ?></td><td><a href="javascript:void(0);" onclick="sendData('messages','get','<?= $data->mid; ?>','delete');">Delete</a></td><td><a href="javascript:void(0);" onclick="sendData('messages','get','<?= $data->fromUID; ?>','userDel');">Delete All From This User</a></td></tr><?
}
?>
</table>
<table border="0"><tr><td align="right"><a href="javascript:void(0);" onclick="sendData('messages','get','all','delete');">Delete All</a></td></tr></table>
<?

echo "Query Count: ".$s->queryCount."<br>";
$pagegen->stop();
print('page generation time: ' . $pagegen->gen());
?>