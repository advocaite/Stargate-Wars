<?
include("config.php");
$s = new Game();
if($_GET['val'])
{
	$query = "SELECT users.uname,userdata.uid,race.r_name as race, rank.overall as rank FROM `users`,race,userdata,rank WHERE `uname` LIKE '".$_GET['val']."%' AND userdata.uid = users.uid AND race.rid = userdata.rid and rank.uid = userdata.uid ORDER BY rank";
	$q = $s->query($query);
	$str = "{ \"result\": [";
	while ($data = mysql_fetch_object($q))
	{
		$str .= "[\"$data->uname\", \"$data->race\", \"$data->rank\",\"$data->uid\" ],";
	}
	$str .= "[]]}";
	echo $str;
}

?>